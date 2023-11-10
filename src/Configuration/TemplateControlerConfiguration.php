<?php
namespace Configuration;

/**
 * Description of ComponentComfiguration
 *
 * @author pes2704
 */
class TemplateControlerConfiguration implements TemplateControlerConfigurationInterface {
//                'templates.defaultExtension' => '.php',
//                // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného typu
//                'templates.folders' => [
//                    'author'=>[self::WEB_TEMPLATES_COMMON.'author/'],   //jen v common
//                    'paper' => [self::WEB_TEMPLATES_SITE.'paper/', self::WEB_TEMPLATES_COMMON.'paper/'],
//                    'article' => [self::WEB_TEMPLATES_SITE.'article/', self::WEB_TEMPLATES_COMMON.'article/'],
//                    'multipage' => [self::WEB_TEMPLATES_SITE.'multipage/', self::WEB_TEMPLATES_COMMON.'multipage/'],
//                    ],


    private $defaultExtension;
    private $folders;

    public function __construct(
            string $defaultExtension,
            array $folders
            ) {
        $this->defaultExtension = $defaultExtension;
        $this->folders = $folders;
    }

    public function getDefaultExtension() {
        return $this->defaultExtension;
    }

    public function getFolders() {
        return $this->folders;
    }

}
