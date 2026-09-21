'use strict';

const fs = require('fs');
const path = require('path');
const less = require('less');

const root = path.resolve(__dirname, '..');
const srcDir = path.join(root, 'local', 'site', 'common', 'less');
const destDir = path.join(root, 'public', 'site', 'common', 'css');
const localSiteDir = path.join(root, 'local', 'site');
const watchMode = process.argv.includes('--watch');

function listEntryFiles() {
    return fs.readdirSync(srcDir)
        .filter((name) => name.endsWith('.less') && !name.startsWith('_'))
        .sort();
}

function relativeFromRoot(filePath) {
    return path.relative(root, filePath).split(path.sep).join('/');
}

async function compileFile(fileName) {
    const srcPath = path.join(srcDir, fileName);
    const destName = fileName.replace(/\.less$/i, '.css');
    const destPath = path.join(destDir, destName);
    const input = fs.readFileSync(srcPath, 'utf8');

    const result = await less.render(input, {
        filename: srcPath,
        paths: [path.dirname(srcPath), localSiteDir],
        rewriteUrls: 'all',
        sourceMap: {
            sourceMapURL: destName + '.map',
            outputSourceFiles: true
        }
    });

    fs.mkdirSync(destDir, { recursive: true });
    fs.writeFileSync(destPath, result.css);
    if (result.map) {
        fs.writeFileSync(destPath + '.map', result.map);
    }
    console.log('Compiled ' + relativeFromRoot(srcPath) + ' -> ' + relativeFromRoot(destPath));
}

async function compileAll() {
    fs.mkdirSync(destDir, { recursive: true });
    const files = listEntryFiles();
    if (files.length === 0) {
        throw new Error('No .less files found in ' + relativeFromRoot(srcDir));
    }
    for (const fileName of files) {
        await compileFile(fileName);
    }
}

function isWatchedLessFile(filePath) {
    const relative = relativeFromRoot(filePath);
    if (!relative.toLowerCase().endsWith('.less')) {
        return false;
    }
    if (relative.includes('/semantic/')) {
        return false;
    }
    return (
        relative === 'local/site/site-name.less' ||
        relative === 'local/site/site-definitions.less' ||
        relative.startsWith('local/site/common/less/') ||
        relative.startsWith('local/site/common/templates/') ||
        /^local\/site\/[^/]+\/less\//.test(relative) ||
        /^local\/site\/[^/]+\/templates\//.test(relative)
    );
}

function watchDirectory(dirPath) {
    if (!fs.existsSync(dirPath)) {
        return;
    }
    fs.watch(dirPath, { recursive: true }, (_eventType, filename) => {
        if (!filename) {
            scheduleCompileAll();
            return;
        }
        const changedPath = path.join(dirPath, filename);
        if (isWatchedLessFile(changedPath)) {
            scheduleCompileAll();
        }
    });
}

let compileTimer = null;
let compiling = false;
let compileAgain = false;

function scheduleCompileAll() {
    clearTimeout(compileTimer);
    compileTimer = setTimeout(() => {
        runWatchedCompile();
    }, 150);
}

async function runWatchedCompile() {
    if (compiling) {
        compileAgain = true;
        return;
    }
    compiling = true;
    try {
        await compileAll();
    } catch (error) {
        console.error('LESS compile failed');
        console.error(error.message || error);
    } finally {
        compiling = false;
        if (compileAgain) {
            compileAgain = false;
            scheduleCompileAll();
        }
    }
}

function startWatch() {
    watchDirectory(srcDir);
    watchDirectory(path.join(localSiteDir, 'common', 'templates'));
    fs.watch(localSiteDir, (_eventType, filename) => {
        if (filename === 'site-name.less' || filename === 'site-definitions.less') {
            scheduleCompileAll();
        }
    });

    for (const entry of fs.readdirSync(localSiteDir, { withFileTypes: true })) {
        if (!entry.isDirectory() || entry.name === 'common') {
            continue;
        }
        watchDirectory(path.join(localSiteDir, entry.name, 'less'));
        watchDirectory(path.join(localSiteDir, entry.name, 'templates'));
    }

    console.log('Watching LESS -> ' + relativeFromRoot(destDir));
    console.log('Active site is set in local/site/site-name.less');
}

async function main() {
    if (watchMode) {
        startWatch();
        await compileAll();
        return;
    }
    await compileAll();
}

main().catch((error) => {
    console.error('LESS compile failed');
    console.error(error.message || error);
    process.exit(1);
});
