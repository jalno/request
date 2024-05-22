<?php

namespace packages\request\Listeners;

use packages\base\DB;
use packages\base\DB\Parenthesis;
use packages\base\Translator;
use packages\request\Authentication;
use packages\request\Authorization;
use packages\request\Process as Request;
use packages\userpanel;
use packages\userpanel\Events\Search as Event;
use packages\userpanel\Search as SearchHandler;
use packages\userpanel\Search\Link;

class Search
{
    public function find(Event $e)
    {
        if (Authorization::is_accessed('search') and Authorization::is_accessed('view')) {
            $this->requests($e->word);
        }
    }

    public function requests(string $word)
    {
        $types = Authorization::childrenTypes();
        $request = new Request();
        $parenthesis = new Parenthesis();
        foreach (['title'] as $item) {
            $parenthesis->where($item, $word, 'contains', 'OR');
        }
        DB::join('userpanel_users', 'userpanel_users.id=request_processes.user', 'LEFT');
        if ($types) {
            DB::where('userpanel_users.type', $types, 'in');
        } else {
            DB::where('userpanel_users.id', Authentication::getID());
        }
        foreach (['name', 'lastname', 'email', 'cellphone', 'phone'] as $item) {
            $parenthesis->where("userpanel_users.{$item}", $word, 'contains', 'OR');
        }
        $request->where($parenthesis);
        foreach ($request->get(null, 'request_processes.*') as $request) {
            $this->addRequest($request);
        }
    }

    private function addRequest(Request $request)
    {
        $statusTxt = '';
        switch ($request->status) {
            case Request::done:		$statusTxt = 'request.process.status.done';
                break;
            case Request::read:		$statusTxt = 'request.process.status.read';
                break;
            case Request::unread:		$statusTxt = 'request.process.status.unread';
                break;
            case Request::disagreement:$statusTxt = 'request.process.status.disagreement';
                break;
            case Request::running: 	$statusTxt = 'request.process.status.running';
                break;
            case Request::failed: 		$statusTxt = 'request.process.status.failed';
                break;
            case Request::cancel: 		$statusTxt = 'request.process.status.cancel';
                break;
        }
        $result = new Link();
        $result->setLink(userpanel\url('requests/view/'.$request->id));
        $result->setTitle(Translator::trans('request.search.title', [
            'title' => $request->title,
        ]));
        $result->setDescription(Translator::trans('request.search.description', [
            'user' => $request->user->getFullName(),
            'status' => Translator::trans($statusTxt),
        ]));
        SearchHandler::addResult($result);
    }
}
