<?php
namespace View;

/**
 * Description of Includer
 *
 * @author pes2704
 */
class Includer implements IncluderInterface {
    public function protectedIncludeScope($includeFilePath, array $context=[]) {
        if ($context) {
            extract($context);
        }
        try {
            $level = ob_get_level();
            ob_start();
            include $includeFilePath;
            $result = ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
        return $result;
    }
}
