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
//            'component.template.flash' => self::RED_TEMPLATES_COMMON.'layout/info/flashMessage.php',
//            'component.template.login' => self::RED_TEMPLATES_COMMON.'layout/status/login.php',
//            'component.template.register' => self::RED_TEMPLATES_SITE.'layout/modal/register-with-exhibitor-representative.php',
//            'component.template.logout' => self::RED_TEMPLATES_COMMON.'layout/status/logout.php',
//            'component.template.useraction' => self::RED_TEMPLATES_COMMON.'layout/status/user_action.php',
//            'component.template.statusboard' => self::RED_TEMPLATES_COMMON.'layout/info/statusBoard.php',
//            'component.template.controleditmenu' => self::RED_TEMPLATES_COMMON.'layout/status/controlEditMenu.php',


    private $logsDirectory;
    private $logsRender;
    private $templateFlash;
    private $templateLogin;
    private $templateRegister;
    private $templateLogout;
    private $templateUserAction;
    private $templateStatusBoard;
    private $templateControlEditMenu;

    public function __construct(
            $logsDirectory,
            $logsRender,
            $templateFlash,
            $templateLogin,
            $templateRegister,
            $templateLogout,
            $templateUserAction,
            $templateStatusBoard,
            $templateControlEditMenu
            ) {
        $this->logsDirectory = $logsDirectory;
        $this->logsRender = $logsRender;
        $this->templateFlash = $templateFlash;
        $this->templateLogin = $templateLogin;
        $this->templateRegister = $templateRegister;
        $this->templateLogout = $templateLogout;
        $this->templateUserAction = $templateUserAction;
        $this->templateStatusBoard = $templateStatusBoard;
        $this->templateControlEditMenu = $templateControlEditMenu;
    }

    public function getLogsDirectory() {
        return $this->logsDirectory;
    }

    public function getLogsRender() {
        return $this->logsRender;
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

    public function getTemplateStatusBoard() {
        return $this->templateStatusBoard;
    }

    public function getTemplateControlEditMenu() {
        return $this->templateControlEditMenu;
    }


}
