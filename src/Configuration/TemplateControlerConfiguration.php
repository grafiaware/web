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
//                    'author'=>[self::RED_TEMPLATES_COMMON.'author/'],   //jen v common
//                    'paper' => [self::RED_TEMPLATES_SITE.'paper/', self::RED_TEMPLATES_COMMON.'paper/'],
//                    'article' => [self::RED_TEMPLATES_SITE.'article/', self::RED_TEMPLATES_COMMON.'article/'],
//                    'multipage' => [self::RED_TEMPLATES_SITE.'multipage/', self::RED_TEMPLATES_COMMON.'multipage/'],
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
