<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Illuminate\Http\Request;
use Session;

class CommentController extends Controller
{
  public function __construct() {

          $this->middleware('auth');
      }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $comments = Comment::orderBy('id', 'desc')->paginate(12);

        return view('comments.index')->withComments($comments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post_id)
    {

        $this->validate($request, [
          'name' => 'required|max:255',
          'email'=> 'required|email|max:255',
          'comment' => 'required|min:5|max:2500'
        ]);
        $post = Post::find($post_id);

        $comment = new Comment();
        $comment->name = $request->name;
        $comment->email = $request->email;
        $comment->comment = $request->comment;
        $comment->approved = true;

        $comment->post()->associate($post);

        $comment->save();
        Session::flash('success', 'Udało się utworzyć komentarz!');

        return redirect()->route('blog.single', [$post->slug]);



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comment = Comment::find($id);
        return view('comments.edit')->withComment($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $comment = Comment::find($id);

      $this->validate($request, array('comment' => 'required|min:5|max:2500') );

      $comment->comment = $request->comment;
      $comment->save();
      $post_comment = $comment->post->id;

      Session::flash('success', 'Zaktualizowałeś komentarz');
      return redirect()->route('posts.show', $post_comment);

    }

    public function delete($id) {

      $comment = Comment::find($id);

      return view('comments.delete')->withComment($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $id)
    {
      $comment = Comment::find($id);
      $comment->delete();
      $post_comment = $comment->post->id;
      Session::flash('success', 'Udało ci się usunąć komentarz!');
      return redirect()->route('posts.show', $post_comment);
    }
}
