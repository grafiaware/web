# Contributing

## IDE configuration

This repo may include a shared **`.vscode/`** baseline (`launch.json`, recommended extensions, optional tasks) for VS Code and Cursor. NetBeans uses **`nbproject/`** separately.

Agent / Cursor guidance for whether to commit `.vscode` lives in:

1. **User Rules** (Cursor Settings → Rules) — preferred when set locally for all projects  
2. **`.cursor/rules/shared-vscode.mdc`** — project fallback; agents should use it **only if** the same policy is **not** already in the developer’s User Rules
