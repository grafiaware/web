<?php
namespace Configuration;


/**
 *
 * @author pes2704
 */
interface ComponentConfigurationInterface {

    public function getLogsDirectory();

    public function getLogsRender();

    public function getTemplatepathPaper();

    public function getTemplatepathArticle();
    
    public function getTemplatepathMultipage();

    public function getTemplateFlash();

    public function getTemplateLogin();

    public function getTemplateRegister();

    public function getTemplateLogout();

    public function getTemplateUserAction();

    public function getTemplateStatusBoard();

    public function getTemplateControlEditMenu();
}
