<?php

namespace forge12\contactform7\CF7Captcha {
    if(!defined('ABSPATH')){
        exit;
    }
    /**
     *
     */
    class UIMenu
    {
        /**
         * UI constructor.
         * @param $slug
         */
        public function __construct()
        {
            add_action('f12_cf7_captcha_admin_menu', array($this,'render'), 10, 3);
        }

        /**
         *
         * @param array<UIPage> $Pages
         * @param string $active_slug
         * @return void
         */
        public function render($Pages, $active_slug, $plugin_slug)
        {
            if(!is_array($Pages)){
                $Pages = array($Pages);
            }
            ?>
            <nav class="navbar">
                <ul class="navbar-nav">
                    <?php do_action('before-forge12-plugin-menu-' . $plugin_slug); ?>
                    <?php foreach ($Pages as /** @var UIPage $Page */ $Page): ?>
                        <li class="forge12-plugin-menu-item">
                            <?php
                            $class = '';

                            $slug = $plugin_slug.'_'.$Page->getSlug();

                            if($Page->isDashboard()){
                                $slug =$plugin_slug;
                            }

                            if ($Page->getSlug() == $active_slug || ($Page->isDashboard() && empty($active_slug))) {
                                $class = 'active';
                            }

                            ?>
                            <a href="<?php echo esc_url(admin_url('admin.php')); ?>?page=<?php echo esc_attr($slug); ?>"
                               title="<?php echo esc_attr($Page->getTitle()); ?>"
                               class="<?php echo esc_attr($class).' '.esc_attr($Page->getClass()); ?>">
                                <?php echo esc_html($Page->getTitle()); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php do_action('after-forge12-plugin-menu-' . $plugin_slug); ?>
                </ul>
            </nav>
            <?php
        }
    }

    new UIMenu();
}