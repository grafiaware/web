<?php
namespace Configuration;

/**
 * Description of ComponentComfiguration
 *
 * @author pes2704
 */
class TemplateControlerConfiguration implements TemplateControlerConfigurationInterface {
//                'templates.defaultFilename' => 'template.php',
//                'templates.authorFolder' => [
//                    self::RED_TEMPLATES_SITE.'author/',
//                    self::RED_TEMPLATES_COMMON.'author/',
//                    ],
//                    'templates.paperFolder' => [
//                    self::RED_TEMPLATES_SITE.'paper/',
//                    self::RED_TEMPLATES_COMMON.'paper/',
//                    ],
//                // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného názvu
//                'templates.articleFolder' => [
//                    self::RED_TEMPLATES_SITE.'article/',
//                    self::RED_TEMPLATES_COMMON.'article/',
//                    ],
//                'templates.multipageFolder' => [
//                    self::RED_TEMPLATES_SITE.'multipage/',
//                    self::RED_TEMPLATES_COMMON.'multipage/',
//                    ]


    private $defaultFilename;
    private $authorFolder;
    private $paperFolder;
    private $articleFolder;
    private $multipageFolder;

    public function __construct(
            string $defaultFilename,
            array $authorFolder,
            array $paperFolder,
            array $articleFolder,
            array $multipageFolder
            ) {
        $this->defaultFilename = $defaultFilename;
        $this->authorFolder = $authorFolder;
        $this->paperFolder = $paperFolder;
        $this->articleFolder = $articleFolder;
        $this->multipageFolder = $multipageFolder;
    }
    
    public function getDefaultFilename() {
        return $this->defaultFilename;
    }

    public function getAuthorFolder() {
        return $this->authorFolder;
    }

    public function getPaperFolder() {
        return $this->paperFolder;
    }

    public function getArticleFolder() {
        return $this->articleFolder;
    }

    public function getMultipageFolder() {
        return $this->multipageFolder;
    }



}
