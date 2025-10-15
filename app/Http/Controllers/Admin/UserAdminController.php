<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $query = User::query()->with('role');

        if ($q) {
            $query->where(function($x) use ($q){
                $x->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%");
            });
        }
        $usuarios = $query->orderBy('id','desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('usuarios','q'));
    }

    // Reset de password inmediato (admin fuerza una nueva)
    public function resetPassword(int $id)
    {
        $user = User::findOrFail($id);
        $pass_temporal = 'Cambio123!'; // cámbialo/lee del request si quieres
        $user->password = Hash::make($pass_temporal);
        $user->must_change_password = true; // para forzar cambio tras login
        $user->save();

        return back()->with('ok', "Password reseteado a: $pass_temporal");
    }

    // Bloqueo / desbloqueo
    public function block(int $id)   { return $this->toggleBlock($id, true); }
    public function unblock(int $id) { return $this->toggleBlock($id, false); }

    private function toggleBlock(int $id, bool $block)
    {
        $user = User::findOrFail($id);
        $user->blocked_at = $block ? now() : null;
        $user->save();
        return back()->with('ok', $block ? 'Usuario bloqueado.' : 'Usuario desbloqueado.');
    }
}
