<?php

namespace Flextype;

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flextype\Component\I18n\I18n;
use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

//
// If isAdminArea
//
if (Admin::isAdminArea()) {

    // Ensure vendor libraries exist
    !is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

    // Register The Auto Loader
    $loader = require_once $autoload;

    //
    // Add listner for onCurrentPageBeforeLoaded event
    //
    Event::addListener('onCurrentEntryBeforeProcessed', function() {

        // Add navigation links
        NavigationManager::addItem('content', 'entries', '<i class="far fa-newspaper"></i>' . __('admin_entries', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/entries', ['class' => 'nav-link']);
        NavigationManager::addItem('extends', 'fieldsets', '<i class="fas fa-list"></i>' . __('admin_fieldsets', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/fieldsets', ['class' => 'nav-link']);
        NavigationManager::addItem('extends', 'templates', '<i class="fas fa-layer-group"></i>' . __('admin_templates', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/templates', ['class' => 'nav-link']);
        NavigationManager::addItem('extends', 'snippets', '<i class="far fa-file-code"></i>' . __('admin_snippets', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/snippets', ['class' => 'nav-link']);
        NavigationManager::addItem('extends', 'plugins', '<i class="fas fa-plug"></i>' . __('admin_plugins', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/plugins', ['class' => 'nav-link']);
        NavigationManager::addItem('settings', 'settings', '<i class="fas fa-cog"></i>' . __('admin_settings', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/settings', ['class' => 'nav-link']);
        NavigationManager::addItem('settings', 'infomation', '<i class="fas fa-info"></i>' . __('admin_information', Registry::get('settings.locale')), Http::getBaseUrl() . '/admin/information', ['class' => 'nav-link']);

        if (Registry::get('settings.locale') == 'ru') {
            NavigationManager::addItem('help', 'documentation', '<i class="far fa-question-circle"></i>' . __('admin_getting_help', Registry::get('settings.locale')), 'http://flextype.org/ru/documentation/guide/basics/getting-help', ['class' => 'nav-link', 'target' => '_blank']);
        } else {
            NavigationManager::addItem('help', 'documentation', '<i class="far fa-question-circle"></i>' . __('admin_getting_help', Registry::get('settings.locale')), 'http://flextype.org/en/documentation/guide/basics/getting-help', ['class' => 'nav-link', 'target' => '_blank']);
        }

        // Initializes the Notification service.
        Notification::init();

        // Get Admin Instance
        Admin::getInstance();
    });
}

class Admin
{
    /**
     * An instance of the Admin class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone()
    {
    }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup()
    {
    }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    protected function __construct()
    {
        Admin::init();
    }

    /**
     * Init Flextype Admin
     *
     * @access private
     */
    private static function init() : void
    {
        // Set Default Admin locale
        I18n::$locale = Registry::get('settings.locale');

        if (UsersManager::isLoggedIn()) {
            Admin::getAdminArea();
        } else {
            if (UsersManager::isUsersExists()) {
                UsersManager::getAuthPage();
            } else {
                UsersManager::getRegistrationPage();
            }
        }

        // Event: onBeforeRequestShutdown
        Event::dispatch('onBeforeRequestShutdown');

        // Shutdown request
        Http::requestShutdown();
    }


    /**
     * Flextype Admin Area
     *
     * @access private
     */
    private static function getAdminArea() : void
    {

        // Event: onAdminArea
        Event::dispatch('onAdminArea');

        // Route the Uri
        Http::getUriSegment(1) == '' and DashboardManager::getDashboardManager();
        Http::getUriSegment(1) == 'entries' and EntriesManager::getEntriesManager();
        Http::getUriSegment(1) == 'fieldsets' and FieldsetsManager::getFieldsetsManager();
        Http::getUriSegment(1) == 'templates' and TemplatesManager::getTemplatesManager();
        Http::getUriSegment(1) == 'snippets' and SnippetsManager::getSnippetsManager();
        Http::getUriSegment(1) == 'plugins' and PluginsManager::getPluginsManager();
        Http::getUriSegment(1) == 'information' and InformationManager::getInformationManager();
        Http::getUriSegment(1) == 'settings' and SettingsManager::getSettingsManager();
        Http::getUriSegment(1) == 'profile' and UsersManager::getProfileManager();
        Http::getUriSegment(1) == 'logout' and UsersManager::logout();
    }


    /**
     * Check is Admin Area
     *
     * @access public
     */
    public static function isAdminArea()
    {
        return (Http::getUriSegment(0) == 'admin') ? true : false;
    }

    /**
     * Get the Admin instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Admin::$instance)) {
            Admin::$instance = new self;
        }

        return Admin::$instance;
    }
}
