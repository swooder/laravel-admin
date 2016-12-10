<?php

namespace Tests\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Tests\Models\Tag;
use Tests\Models\User;

class UserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('All users');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('Edit user');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('Create user');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->id('ID')->sortable();

            $grid->username();
            $grid->email();
            $grid->mobile();
            $grid->full_name();
            $grid->avatar()->value(function ($avatar) {
                return "<img src='{$avatar}' />";
            });
            $grid->profile()->postcode('Post code');
            $grid->profile()->address();
            $grid->position('Position');
            $grid->profile()->color();
            $grid->profile()->start_at('开始时间');
            $grid->profile()->end_at('结束时间');

            $grid->column('column1_not_in_table')->display(function () {
                return 'full name:'.$this->full_name;
            });

            $grid->column('column2_not_in_table')->display(function () {
                return $this->email.'#'.$this->profile['color'];
            });

            $grid->tags()->value(function ($tags) {
                $tags = collect($tags)->map(function ($tag) {
                    return "<code>{$tag['name']}</code>";
                })->toArray();

                return implode('', $tags);
            });

            $grid->created_at();
            $grid->updated_at();

            $grid->filter(function ($filter) {
                $filter->like('username');
                $filter->like('email');
                $filter->like('profile.postcode');
                $filter->between('profile.start_at')->datetime();
                $filter->between('profile.end_at')->datetime();
            });

            $grid->rows(function (Grid\Row $row) {
                if ($row->id % 2 == 0) {
                    $row->actions()->add(function ($row) {
                        return '<a href="/" class="btn btn-xs btn-danger">detail</a>';
                    });
                }
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {
            $form->disableDeletion();

            $form->display('id', 'ID');
            $form->text('username');
            $form->email('email')->rules('required');
            $form->mobile('mobile');
            $form->image('avatar')->help('上传头像', 'fa-image');
            $form->password('password');

            $form->divide();

            $form->text('profile.first_name');
            $form->text('profile.last_name');
            $form->text('profile.postcode')->help('Please input your postcode');
            $form->textarea('profile.address')->rows(15);
            $form->map('profile.latitude', 'profile.longitude', 'Position');
            $form->color('profile.color');
            $form->datetime('profile.start_at');
            $form->datetime('profile.end_at');

            $form->multipleSelect('tags', 'Tags')->options(Tag::all()->pluck('name', 'id')); //->rules('max:10|min:3');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            $form->html('<a html-field>html...</a>');
        });
    }
}
