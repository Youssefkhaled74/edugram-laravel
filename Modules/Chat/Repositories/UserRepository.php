<?php
namespace Modules\Chat\Repositories;

use App\Models\User;
use Modules\Chat\Entities\BlockUser;


class UserRepository {

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function Search($keywords){
        $blocks = BlockUser::where('block_by', auth()->id())->pluck('block_to')->toArray();
        return User::where('id', '<>', auth()->id())
            ->where(function ($query) use ($keywords) {
//                $query->whereRaw("concat(first_name, ' ', last_name) like '%$keywords%'");
//                $query->orWhereRaw("concat(last_name, ' ', first_name) like '%$keywords%'");
                $query->orWhere('email', 'LIKE', '%'.$keywords.'%');
                $query->orWhere('name', 'LIKE', '%'.$keywords.'%');
            })
            ->whereNotIn('id', $blocks)
            ->paginate(10);
    }

    public function profileUpdate($data)
    {
        return User::find(auth()->id())->update($data);
    }

    public function blockAction($type, $user)
    {
        if ($type == 'block'){
            BlockUser::create([
                'block_by' => auth()->id(),
                'block_to' => $user
            ]);
        }else{
            $block = BlockUser::where('block_by', auth()->id())->where('block_to', $user)->first();
            $block->delete();
        }

        return true;
    }

    public function allBlockedUsers()
    {
        $blocks = BlockUser::where('block_by', auth()->id())->pluck('block_to')->toArray();
        return User::whereIn('id', $blocks)->get();
    }

}
