<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\PrepareNoticeRequest;
use App\Notice;
use App\Provider;
use Auth;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;

/**
 * Class NoticesController
 * @package App\Http\Controllers
 */
class NoticesController extends Controller {

    /**
     * creates a new notices controller instance
     *
     */
    public function __construct()
    {
        $this->middleware('auth');

        //dd("im here");

        parent::__constructor();
    }

    /**
     * Show all notices
     *
     * @return string
     */
    public function index()
    {
        $notices = $this->user->notices;

        //dd($notices);

        return view('notices.index', compact('notices'));
    }

    /**
     * Show a page to create a new notice
     *
     * @return \Response
     */
    public function create()
    {
        // get list of providers
        $providers = Provider::lists('name', 'id');

        //dd("im here");

        // load a view to create a new notices
        return view('notices.create', compact('providers'));
    }

    /**
     * Ask the user to confirm the DMCA that will be delivered.
     *
     * Once validation has passed via the PrepareNoticeRequest, show all data entered by the user via the form.
     *
     * @param PrepareNoticeRequest $request
     * @return array
     */
    public function confirm(PrepareNoticeRequest $request)
    {
        $template = $this->compileDmcaTemplate($data = $request->all());

        // store the request data, $data, into the _SESSION super global, just for one page request.
        session()->flash('dmca', $data);
        //dd("im here");
        // send the derived view to the below view.
        return view('notices.confirm', compact('template'));

    }

    /**
     * Store a new DMCA notice.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        // Form data is flashed.  Get with session()->('dmc');
        // Template is in request.  Request::input('template');
        // So build up a Notice object (create table too)
        // persist it with this data.
        $notice = $this->createNotice($request);

           //dd($notice);

        // And then fire off the email.
        \Mail::queue(['text' => 'emails.dmca'], compact('notice'), function($message) use ($notice) {
            $message->from($notice->getOwnerEmail())
                    ->to($notice->getRecipientEmail())
                    ->subject('DMCA Notice');
        });

        flash('Your DMCA notice has been delivered!');

        return redirect('notices');

    }

    public function update($noticeId, Request $request)
    {
        // sets #isRremoved, a boolean, based of if the user checked content_removed column on the Your Notices form.
        $isRemoved = $request->has('content_removed');

        Notice::findOrFail($noticeId)
            ->update(['content_removed' => $isRemoved]);

        // returns to the previous page.  The below is temporary; now handled by Ajax.
        // return redirect()->back();
    }


    /**
     * Compile the DMCA template from the form data.
     *
     * @param $data
     * @return mixed
     */
    public function compileDmcaTemplate($data)
    {
        // contact the data from the form with $auth user data of name and email
        $data = $data + [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ];

        // derive a view via compiling a view with a specific path using the $data data
        return view()->file(app_path('Http/Template/dmca.blade.php'), $data);
    }

    /**
     * Create and persist a new DMCA notice.
     *
     * @param Request $request
     */
    public function createNotice(Request $request)
    {
        $data = session()->get('dmca');

        $notice = Notice::open($data)->userTemplate($request->input('template'));

        $this->user->notices()->save($notice);

        return $notice;
    }

    /**
     * Create and persist a new DMCA notice.
     *
     * @param Request $request
     */
    public function createNoticeANOTHER_APPROACH(Request $request)
    {
        $notice = session()->get('dmca') + ['template' => $request->input('template')];

        Auth::user()->notices()->create($notice);
    }



}
