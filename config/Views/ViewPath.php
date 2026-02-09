<?php

namespace Lovillela\BlogApp\Config\Views;

enum ViewPath: string{
  case BASE_PATH = __DIR__ . '/../../src/Views/';
  case BASE_VIEW = (string)BASE_PATH . 'BaseView.php';
  case ADMIN_BASE_PATH = BASE_PATH . 'Admin/';
  case FRONTEND_BASE_PATH = BASE_PATH . 'Frontend/';
  /**
   * Admin views
   */
  case ADMIN_ADD_USER_VIEW = ADMIN_BASE_PATH . 'AddUserView.php';
  case ADMIN_DASHBOARD_VIEW = ADMIN_BASE_PATH . 'DashBoardView.php';
  case ADMIN_LOGIN_VIEW = ADMIN_BASE_PATH . 'LoginView.php';
  /**
   * Fim/End Admin Views
   */

  /**
   * Frontend views
   */
  case FRONTEND_DASHBOARD_VIEW_REGULARUSER = FRONTEND_BASE_PATH . 'DashBoardViewRegularUser.php';
  case FRONTEND_HOMEPAGE_VIEW = FRONTEND_BASE_PATH . 'HomePageView.php';
  case FRONTEND_LOGIN_VIEW_REGULAR_USER = FRONTEND_BASE_PATH . 'LoginViewRegularUser.php';
  case FRONTEND_POSTFOR_MVIEW = FRONTEND_BASE_PATH . 'PostFormView.php';
  case FRONTEND_POST_HOME_VIEW = FRONTEND_BASE_PATH . 'PostHomeView.php';
  case FRONTEND_POST_VIEW = FRONTEND_BASE_PATH . 'PostView.php';
  case FRONTEND_SIGNUP_VIEW = FRONTEND_BASE_PATH . 'SignupView.php';
  /**
   * Fim/End Admin Views
   */
}
