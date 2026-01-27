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
    $dashboardLink = "https://$_SERVER[HTTP_HOST]/dashboard";
    header('Location: ' . $dashboardLink);
  }

  public function redirectToAdminDashboard() {
    $dashboardLink = "https://$_SERVER[HTTP_HOST]/admin/dashboard";
    header('Location: ' . $dashboardLink);
  }
}
