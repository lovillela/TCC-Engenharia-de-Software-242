<?php

namespace Lovillela\BlogApp\Services;

class RedirectService{
  public function redirectToTrailingSlash() {
    $fullLink = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]/";
    header('Location: '. $fullLink , true, 301);
    exit();
  }

  public function redirectToHome(){
    header('Location: /');
    exit();
  }

  public function redirectToUserDashboard() {
    $dashboardLink = "https://$_SERVER[HTTP_HOST]/dashboard/";
    header('Location: ' . $dashboardLink);
    exit;
  }

  public function redirectToAdminDashboard() {
    $dashboardLink = "https://$_SERVER[HTTP_HOST]/admin/dashboard/";
    header('Location: ' . $dashboardLink);
    exit;
  }

  public function redirectToAdminUsersList() {
    $AdminUsersListDashboardLink = "https://$_SERVER[HTTP_HOST]/admin/dashboard/list/users/";
    header('Location: ' . $AdminUsersListDashboardLink);
    exit;
  }

  public function redirectToAdminPostsList() {
    $AdminPostsListDashboardLink = "https://$_SERVER[HTTP_HOST]/admin/dashboard/list/posts/";
    header('Location: ' . $AdminPostsListDashboardLink);
    exit;
  }

  public function redirectToPostBySlug(string $postSlug) {
    $postLink = "https://$_SERVER[HTTP_HOST]/post/$postSlug/";
    header('Location:' . $postLink);
  }
}
