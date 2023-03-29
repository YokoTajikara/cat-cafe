<?php

namespace App\Http\Controllers\Admin;


use App\Models\Blog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use Illuminate\Http\Request;
use Storage;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of the resource.
	 * ブログ一覧画面
     */
    public function index()
    {
		$blogs = Blog::latest('updated_at')->paginate(10);
        return view('admin.blogs.index' , ['blogs' => $blogs]);
    }

    /**
     * Show the form for creating a new resource.
	 * ブログ投稿画面
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
	 * ブログ投稿処理
     */
    public function store(StoreBlogRequest $request)
    {
		//storeに一度保管。なぜ？？
        $saveImagePath = $request->file('image')->store('blogs','public');
		//()の中に配列を指定することで一括でデータをセットできる。validatedメソッド使い、検証されたデータをセット
		$blog = new Blog($request->validated());
		//imageカラムにはフォームから送信されたファイルデータではなく、アップロードされたファイルパスを指定する必要がある
		$blog->image = $saveImagePath;
		//blogメソッドのsaveを呼び出しDBに保存
		$blog->save();

		return to_route('admin.blogs.index')->with('success','ブログを投稿しよう');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
	 * 指定したIDのブログの編集画面
     */
    public function edit(Blog $blog)
    {
		return view('admin.blogs.edit', ['blog' => $blog]);
    }

	//指定した/IDのブログの更新処理
	public function update(UpdateBlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
		$updateData = $request->validated();

		//画像を変更する場合
		if($request->has('image')){
			//変更前の画像削除
			Storage::disk('public')->delete($blog->image);
			//変更後の画像をアップロード、保存パスを更新対象データにセット
			$updateData['image'] = $request->file('image')->store('blogs','public');
		}
		$blog->update($updateData);

		return to_route('admin.blogs.index')->with('success','ブログを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blog::findOrFail($id);
		$blog->delete();
		Storage::disk('public')->delete($blog->image);

		return to_route('admin.blogs.index')->with('success','ブログを削除しました');
    }
}
