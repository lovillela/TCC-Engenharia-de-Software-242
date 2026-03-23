<?php

namespace Lovillela\BlogApp\Config\Views;

enum ViewPath: string{

  private const BASE_PATH = __DIR__ . '/../../src/Views/';

  case BASE_VIEW = 'BaseView.php';
  /**************************************************************************/
  /**
   * Admin views
   */
  case ADMIN_ADD_USER = 'Admin/AddUserView.php';
  case ADMIN_DASHBOARD = 'Admin/DashBoardView.php';
  case ADMIN_LOGIN = 'Admin/LoginView.php';
  case ADMIN_LIST_ALL_USERS_POSTS = 'Admin/ListAllUsersPostsView.php';
  case ADMIN_LIST_ALL_USERS = 'Admin/ListAllUsersView.php';
  /**
   * Fim/End Admin Views
   */
  /**************************************************************************/
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
  case FRONTEND_EDIT_POSTFORM = 'Frontend/PostFormEditView.php';
  /**
   * Fim/End Frontend Views
   */
  /**************************************************************************/
  /**
   * Partial views
   */
  case PARTIAL_TEXT_EDITOR = 'Partial/QuillPartialView.php';
  case PARTIAL_POST_LIST = 'Partial/PostListPartialView.php';
  case PARTIAL_USER_LIST = 'Partial/UserListPartialView.php';
    /**
   * Fim/End Partial Views
   */
  /**************************************************************************/
  
  public function getPath() : string {
    return self::BASE_PATH . $this->value;
  }
}
