<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetupDevEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the development environment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up development Enviroment');

        $this->MigrateAndSeedDatabase();
        $user = $this->CreateJohnDoeUser();
        $this->CreatePersonalAccessClient($user);
        $this->CreatePersonalAccessToken($user);


    }

    public function MigrateAndSeedDatabase()
    {
        $this->call('migrate:fresh');
        $this->call('db:seed');
    }

    public function CreateJohnDoeUser()
    {
        $this->info('Creating John Doe User');
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@user.com',
            'password' => bcrypt('password'),
        ]);
        $this->info('John Doe Created');
        $this->warn('Email: john@user.com');
        $this->warn('Password: password');
        return $user;
    }

    /**
     * @param $user
     */
    public function CreatePersonalAccessClient($user)
    {
        $this->call('passport:client', [
            '--personal'=> true,
            '--name' => 'Personal',
            '--user_id' => $user->id
        ]);
    }

    /**
     * @param $user
     */
    public function CreatePersonalAccessToken($user)
    {
        $token = $user->createToken('Authentication Token');
        $this->info('Personal access token created successfully');
        $this->warn("Personal access token:");
        $this->line($token->accessToken);
    }
}
