<?php

namespace Lovillela\BlogApp\Config\Views;

enum ViewPath: string{

  private const BASE_PATH = __DIR__ . '/../../src/Views/';

  case BASE_VIEW = 'BaseView.php';
  /**
   * Admin views
   */
  case ADMIN_ADD_USER = 'Admin/AddUserView.php';
  case ADMIN_DASHBOARD = 'Admin/DashBoardView.php';
  case ADMIN_LOGIN = 'Admin/LoginView.php';
  /**
   * Fim/End Admin Views
   */

  /**
   * Frontend views
   */
  case FRONTEND_DASHBOARD_REGULARUSER = 'Frontend/DashBoardViewRegularUser.php';
  case FRONTEND_HOMEPAGE = 'Frontend/HomePageView.php';
  case FRONTEND_LOGIN_REGULAR_USER = 'Frontend/LoginViewRegularUser.php';
  case FRONTEND_POSTFORM = 'Frontend/PostFormView.php';
  case FRONTEND_POST_HOME = 'Frontend/PostHomeView.php';
  case FRONTEND_POST = 'Frontend/PostView.php';
  case FRONTEND_SIGNUP = 'Frontend/SignupView.php';
  /**
   * Fim/End Admin Views
   */
  public function getPath() : string {
    return self::BASE_PATH . $this->value;
  }
}
