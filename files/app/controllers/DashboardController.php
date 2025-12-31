<? php
/**
 * DashboardController
 * Handles main dashboard and profile management
 */

class DashboardController extends Controller {
    private $userModel;
    private $productModel;
    private $orderModel;
    private $wishlistModel;

    public function __construct() {
        parent:: 