<?php
namespace Configuration;

/**
 * Description of ComponentComfiguration
 *
 * @author pes2704
 */
class ComponentConfiguration implements ComponentConfigurationInterface {
//            'component.logs.directory' => 'Logs/App/Web',
//            'component.logs.render' => 'Render.log',
//            'component.templatepath.paper' => self::RED_TEMPLATES_COMMON.'paper/',
//            'component.templatepath.article' => self::RED_TEMPLATES_COMMON.'article/',
//            'component.template.flash' => self::RED_TEMPLATES_COMMON.'layout/info/flashMessage.php',
//            'component.template.login' => self::RED_TEMPLATES_COMMON.'layout/modal/login.php',
//            'component.template.register' => self::RED_TEMPLATES_SITE.'layout/modal/register-with-exhibitor-representative.php',
//            'component.template.logout' => self::RED_TEMPLATES_COMMON.'layout/modal/logout.php',
//            'component.template.useraction' => self::RED_TEMPLATES_COMMON.'layout/modal/user_action.php',

    private $logsDirectory;
    private $logsRender;
    private $templatepathPaper;
    private $templatepathArticle;
    private $templateFlash;
    private $templateLogin;
    private $templateRegister;
    private $templateLogout;
    private $templateUserAction;

    public function __construct($logsDirectory, $logsRender, $templatepathPaper, $templatepathArticle, $templateFlash, $templateLogin, $templateRegister, $templateLogout, $templateUserAction) {
        $this->logsDirectory = $logsDirectory;
        $this->logsRender = $logsRender;
        $this->templatepathPaper = $templatepathPaper;
        $this->templatepathArticle = $templatepathArticle;
        $this->templateFlash = $templateFlash;
        $this->templateLogin = $templateLogin;
        $this->templateRegister = $templateRegister;
        $this->templateLogout = $templateLogout;
        $this->templateUserAction = $templateUserAction;
    }

    public function getLogsDirectory() {
        return $this->logsDirectory;
    }

    public function getLogsRender() {
        return $this->logsRender;
    }

    public function getTemplatepathPaper() {
        return $this->templatepathPaper;
    }

    public function getTemplatepathArticle() {
        return $this->templatepathArticle;
    }

    public function getTemplateFlash() {
        return $this->templateFlash;
    }

    public function getTemplateLogin() {
        return $this->templateLogin;
    }

    public function getTemplateRegister() {
        return $this->templateRegister;
    }

    public function getTemplateLogout() {
        return $this->templateLogout;
    }

    public function getTemplateUserAction() {
        return $this->templateUserAction;
    }


}
