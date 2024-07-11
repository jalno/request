<?php

namespace packages\request\Listeners\UserPanel\Users;

use packages\base\{View\Error};
use packages\request\Authorization;
use packages\request\Process as Request;
use packages\userpanel\Events as UserpanelEvents;

use function packages\userpanel\Url;

class BeforeDelete
{
    public function check(UserpanelEvents\Users\BeforeDelete $event): void
    {
        $this->checkTicketsClient($event);
    }

    private function checkTicketsClient(UserpanelEvents\Users\BeforeDelete $event): void
    {
        $user = $event->getUser();
        $hasRequests = (new Request())->where('user', $user->id)->has();
        if (!$hasRequests) {
            return;
        }
        $message = t('error.packages.request.error.requests.user.delete_user_warn.message');
        $error = new Error('packages.request.error.requests.user.delete_user_warn');
        $error->setType(Error::WARNING);
        if (Authorization::is_accessed('search')) {
            $message .= '<br> '.t('packages.request.error.requests.user.delete_user_warn.view_requests').' ';
            $error->setData([
                [
                    'txt' => '<i class="fa fa-search"></i> '.t('packages.request.error.requests.user.delete_user_warn.view_requests_btn'),
                    'type' => 'btn-warning',
                    'link' => url('requests', [
                        'user' => $user->id,
                    ]),
                ],
            ], 'btns');
        } else {
            $message .= '<br> '.t('packages.request.error.requests.user.delete_user_warn.view_requests.tell_someone');
        }
        $error->setMessage($message);

        $event->addError($error);
    }
}
