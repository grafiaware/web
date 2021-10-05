<?php
namespace Configuration;


/**
 *
 * @author pes2704
 */
interface TemplateControlerConfigurationInterface {

    public function getDefaultFilename();

    public function getAuthorFolder();

    public function getPaperFolder();

    public function getArticleFolder();

    public function getMultipageFolder();
}
