<?php 
namespace App\Http\View\Composers ;

use Illuminate\View\View;
use App\Models\notifications;

class NotificationsComposer
{
    public function compose(View $view)
    {
        $view->with('notifications', notifications::all());
    }
}
?>