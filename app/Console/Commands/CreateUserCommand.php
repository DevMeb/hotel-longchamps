<?php

namespace App\Console\Commands;

use App\Models\User; // Modèle User
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {name : Le nom de l\'utilisateur}
                            {password? : Le mot de passe de l\'utilisateur (généré automatiquement si non fourni)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer un nouvel utilisateur';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Récupérer les arguments de la commande
        $name = $this->argument('name');
        $password = $this->argument('password'); // Mot de passe aléatoire si non fourni

        // Validation des données
        $validator = Validator::make(compact('name', 'password'), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            $this->error('Les données fournies ne sont pas valides :');
            foreach ($validator->errors()->all() as $error) {
                $this->line("- $error");
            }
            return Command::FAILURE;
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $name,
            'password' => Hash::make($password),
        ]);

        // Retourner un message de succès
        $this->info("Utilisateur créé avec succès !");
        $this->line("Nom : {$user->name}");
        $this->line("Mot de passe : {$password}"); // Si le mot de passe est généré automatiquement

        return Command::SUCCESS;
    }
}
