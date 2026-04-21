<?php
class MY_Loader extends CI_Loader
{
    protected $manualLayoutDetected = false;

    public function view($view, $vars = array(), $return = FALSE)
    {
        $view_name = preg_replace('/\.php$/', '', trim($view));

        $template_views = [
            'templates/layout',
            'templates/user_header',
            'templates/user_sidebar',
            'templates/user_topbar',
            'templates/user_footer',
        ];

        $exclude_views = array_merge($template_views, [
            'admin/balance_sync_view',
            'auth/login',
            'auth/register',
            'auth/blocked',
            'auth/change-password',
            'auth/forgotPassword',
            'templates/auth_header',
            'templates/auth_footer',
        ]);

        if (in_array($view_name, $template_views)) {
            $this->manualLayoutDetected = true;
            return parent::view($view, $vars, $return);
        }

        if ($this->manualLayoutDetected || in_array($view_name, $exclude_views)) {
            return parent::view($view, $vars, $return);
        }

        $content = parent::view($view, $vars, TRUE);
        $vars['content'] = $content;

        return parent::view('templates/layout', $vars, $return);
    }
}