<?php
namespace Configuration;

/**
 * Description of ComponentComfiguration
 *
 * @author pes2704
 */
class TemplateControlerConfiguration implements TemplateControlerConfigurationInterface {
//         [
//            'templates.defaultExtension' => '.php',
//            // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného typu
//            'templates.folders' => [
//                'author'=>[self::WEB_TEMPLATES_COMMON.'red/author/'],   //jen v common
//                'paper' => [self::WEB_TEMPLATES_SITE.'red/paper/', self::WEB_TEMPLATES_COMMON.'red/paper/'],
//                'article' => [self::WEB_TEMPLATES_SITE.'red/article/', self::WEB_TEMPLATES_COMMON.'red/article/'],
//                'multipage' => [self::WEB_TEMPLATES_SITE.'red/multipage/', self::WEB_TEMPLATES_COMMON.'red/multipage/'],
//                ],
//            'templates.list' => [
//                'multipage' => [
//                    [ 'title' => 'template multipage test', 'description' => 'popis',       'url' => 'red/v1/multipagetemplate/test'],
//                ],
//                'article' => [
//                    [ 'title' => 'Šablona pro příspěvek', 'description' => 'Jednoduchá šablona pro vložení textu a obrázku.',       'url' => 'red/v1/articletemplate/web_post'],
//                 ],
//                'paper' => [
//                    [ 'title' => 'template paper default', 'description' => 'popis',       'url' => 'red/v1/papertemplate/default'],
//                ],
//                'author' => [
//                    [ 'title' => 'Kontakt', 'description' => 'Grafia web - kontakt',       'url' => 'red/v1/authortemplate/contact'],
//                ],
//            ]

    private $defaultExtension;
    private $folders;
    private $list;
            
    public function __construct(
            string $defaultExtension,
            array $folders, 
            array $list
            ) {
        $this->defaultExtension = $defaultExtension;
        $this->folders = $folders;
        $this->list = $list;
    }

    public function getDefaultExtension() {
        return $this->defaultExtension;
    }

    public function getFolders() {
        return $this->folders;
    }
    
    public function getList() {
        return $this->list;
    }

}
