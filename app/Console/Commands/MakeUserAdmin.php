<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-user-admin {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign admin role to user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::findOrFail($this->argument('userId'));
        $user->assignRole(Role::ADMIN);

        $this->info("User $user->id is now an admin");
    }
}
