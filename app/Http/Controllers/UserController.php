<?php

namespace App\Http\Controllers;


use App\Grade;
use App\Http\Requests;
use App\Role;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Webpatser\Countries\Countries;

class UserController extends Controller
{
    protected $currentModelName;

    public function __construct()
    {
//        $this->middleware('auth');
        // Fetch the Site Settings object
        $this->currentModelName = trans_choice('crud.user', 1);
        $this->modelPlural = trans_choice('crud.user', 1);
        View::share('currentModelName', $this->currentModelName);
        View::share('modelPlural', $this->modelPlural);


    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = DB::table('users')
            ->leftJoin('countries', 'users.countryId', '=', 'countries.id')
            ->leftJoin('grade', 'users.gradeId', '=', 'grade.id')
            ->select('users.*', 'grade.name as grade', 'countries.name as country', 'countries.flag')
            ->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $user = new User();
        $roles = Role::lists('name', 'id');
        $grades = Grade::lists('name', 'id');
        $countries = Countries::lists('name', 'id');

        return view('users.create', compact('user', 'countries', 'grades', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (Input::file('avatar') != null && Input::file('avatar')->isValid()) {
            $destinationPath = Config::get('constants.AVATAR_PATH');
            $extension = Input::file('avatar')->getClientOriginalExtension(); // getting image extension
            $date = new DateTime();
            $timestamp = $date->getTimestamp();
            $fileName = $timestamp . '.' . $extension; // renameing image
            Input::file('avatar')->move($destinationPath, $fileName); // uploading file to given path
        }

        if (User::create($request->all())) {
            flash('success', Lang::get('core.success'));
        } else
            flash('error', Lang::get('core.fail'));
        return redirect('users');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show($user)
    {
        $user->delete();
        return redirect("users");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(User $user)
    {
//        $user = User::findOrFail($id);
        $roles = Role::lists('name', 'id');
        $grades = Grade::orderBy('order')->lists('name', 'id');
        $countries = Countries::lists('name', 'id');

        return view('users.edit', compact('user', 'countries', 'grades', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $profile
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        if (Input::file('avatar') != null && Input::file('avatar')->isValid()) {
            $destinationPath = Config::get('constants.AVATAR_PATH');
            $extension = Input::file('avatar')->getClientOriginalExtension(); // getting image extension
            $date = new DateTime();
            $timestamp = $date->getTimestamp();
            $fileName = $timestamp . '.' . $extension; // renameing image
            Input::file('avatar')->move($destinationPath, $fileName); // uploading file to given path
        }
        $except = [];
        if (trim(Input::get('roleId')) == '') {
            array_push($except, 'roleId');
        }
        if (trim(Input::get('password')) == '') {
            array_push($except, 'password');

        }
        $data = $request->except($except);



        if ($user->update($data)) {
            flash('success', Lang::get('core.success'));
        } else
            flash('error', Lang::get('core.fail'));


        return redirect("/users");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
//    public function showProfile($id)
//    {
//        $user = User::findOrFail($id);
//        $grades = Grade::lists('name', 'id');
//        $countries = Countries::lists('name', 'id');
//        return view('users.show', compact('user', 'countries', 'grades'));
//    }
}
