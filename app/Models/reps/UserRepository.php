<?php

namespace App\Models\reps;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    protected $table_name = 'tbl_user';
    protected $primaryKey = '_id';
    protected $table_allergen = 'tbl_user_allergen';


    public function findById($id)
    {
        return DB::table($this->table_name)->where($this->primaryKey, $id)->first();
    }

    public function register(array $data): ?int
    {
        try {
            if (isset($data['_name'])) {
                $data['_full_name'] = $data['_name'];
                unset($data['_name']);
            }

            $data['_password'] = Hash::make($data['_password']);
            $data['_created_at'] = now();
            $data['_updated_at'] = now();

            return DB::table($this->table_name)->insertGetId($data);
        } catch (\Exception $e) {
            Log::error('Register failed: ' . $e->getMessage());
            return null;
        }
    }



    public function attemptLogin(string $email, string $password): ?object
    {
        $user = DB::table($this->table_name)
            ->where('_email', $email)
            ->first();

        if ($user && Hash::check($password, $user->_password)) {

            DB::table($this->table_name)
                ->where($this->primaryKey, $user->_id);
            return $user;
        }

        return null;
    }


    public function updateProfile(int $user_id, array $data): bool
    {
        try {
            $data['_updated_at'] = now();
            DB::table($this->table_name)
                ->where($this->primaryKey, $user_id)
                ->update($data);
            return true;
        } catch (\Exception $e) {
            Log::error('Update Profile failed: ' . $e->getMessage());
            return false;
        }
    }


    public function changePassword(int $user_id, string $newPassword): bool
    {
        try {
            DB::table($this->table_name)
                ->where($this->primaryKey, $user_id)
                ->update([
                    '_password' => Hash::make($newPassword),
                    '_updated_at' => now()
                ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Change password failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserDetails(int $user_id): ?object
    {
        return DB::table($this->table_name . ' as u')
            ->leftJoin('tbl_diet_type as d', 'u._diet_type_id', '=', 'd._id')
            ->leftJoin('tbl_goal as g', 'u._goal', '=', 'g._id') 
            ->where('u._id', $user_id)
            ->select(
                'u._id',
                'u._username',
                'u._email',
                'u._full_name',
                'u._avatar',
                'u._dob',
                'u._gender',
                'u._weight_kg',
                'u._height_cm',
                'u._activity_level',
                'u._goal',
                'g._name as goal_name',    
                'd._name as diet_type'      
            )
            ->first();
    }



    public function getUserAllergens(int $user_id): array
    {
        return DB::table('tbl_user_allergen as ua')
            ->join('tbl_allergen as a', 'ua._allergen_id', '=', 'a._id')
            ->where('ua._user_id', $user_id)
            ->select('a._id', 'a._name')
            ->get()
            ->toArray();
    }

    public function updateAllergens(int $user_id, array $allergenIds): bool
    {
        try {
            DB::table('tbl_user_allergen')
                ->where('_user_id', $user_id)
                ->delete();

            $now = now();
            $insertData = array_map(function ($aid) use ($user_id, $now) {
                return [
                    '_user_id' => $user_id,
                    '_allergen_id' => $aid,
                    '_created_at' => $now
                ];
            }, $allergenIds);

            if (!empty($insertData)) {
                DB::table('tbl_user_allergen')->insert($insertData);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Update allergens failed: ' . $e->getMessage());
            return false;
        }
    }

    public function updateGoalAndDietOnly(int $user_id, string $goal, int $diet_type_id): bool
    {
        try {
            return DB::table($this->table_name)
                ->where($this->primaryKey, $user_id)
                ->update([
                    '_goal' => $goal,
                    '_diet_type_id' => $diet_type_id,
                    '_updated_at' => now()
                ]);
        } catch (\Exception $e) {
            Log::error('Update goal and diet failed: ' . $e->getMessage());
            return false;
        }
    }
}
