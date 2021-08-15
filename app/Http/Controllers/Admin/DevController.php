<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\models\PostType;
use App\models\Privacy;
use App\models\ShareType;
use App\models\sourceTypes;
use App\models\State;
use Illuminate\Http\Request;

class DevController extends Controller
{

    public  $states=[
        [
            'name'=> 'accepted',
            'value'=>1
        ],
        [
            'name'=> 'refused',
            'value'=>0
        ],
        [
            'name'=> 'pending',
            'value'=>2
        ],

    ];
    public  $privacy=[
        [
            'name'=> 'public',
        ],
        [
            'name'=> 'private',
        ],


    ];
    public $postType = [
        [
            'name'=> 'post',
        ],
        [
            'name'=> 'service',
        ],
    ];
    public $share = [
        [
            'name'=> 'messgae',
        ],
        [
            'name'=> 'profile',
        ],
        [
            'name'=> 'group'
        ]
    ];
    public $sourceTypes =[
        [
            'name'=> 'post',
        ],
        [
            'name'=> 'comment',
        ],
        [
            'name'=>'user',
        ]
    ];


    public function states(){
        for ($i=0;$i  < count($this->states) ; $i++) {
            State::create([
            'name' => $this->states[$i]['name'],
            'value' =>$this->states[$i]['value']
        ]);
        }
    }
    public function privacy(){
        for ($i=0;$i  < count($this->privacy) ; $i++) {
            Privacy::create([
                'name' => $this->privacy[$i]['name']
            ]);
        }
    }

    public function postType(){
        for ($i=0;$i  < count($this->postType) ; $i++) {
            PostType::create([
                'name' => $this->postType[$i]['name']
            ]);
        }
    }
    public function share(){
        for ($i=0;$i  < count($this->postType) ; $i++) {
            ShareType::create([
                'name' => $this->postType[$i]['name']
            ]);
        }
    }
    public function sourceTypes(){
        for ($i=0;$i  < count($this->sourceTypes) ; $i++) {
            sourceTypes::create([
                'name' => $this->sourceTypes[$i]['name']
            ]);
        }
    }

    public function generate(){
        $this->states();
        $this->privacy();
        $this->postType();
        $this->share();
        $this->sourceTypes();
    }

}
