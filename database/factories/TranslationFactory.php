<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        $locales = ['en', 'fr', 'es'];
        $prefixes = ['button', 'label', 'message', 'title', 'error', 'success', 'warning', 'info', 'placeholder', 'tooltip'];

        $prefix = $this->faker->randomElement($prefixes);
        $key = $prefix.'.'.$this->faker->unique()->slug(3);

        return [
            'locale' => $this->faker->randomElement($locales),
            'key' => $key,
            'content' => $this->generateContent($prefix),
        ];
    }

    public function english(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
        ]);
    }

    public function french(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'fr',
        ]);
    }

    public function spanish(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'es',
        ]);
    }

    private function generateContent(string $prefix): string
    {
        $contentMap = [
            'button' => [
                'en' => ['Submit', 'Cancel', 'Save', 'Delete', 'Edit', 'Add', 'Search', 'Filter', 'Export', 'Import'],
                'fr' => ['Soumettre', 'Annuler', 'Enregistrer', 'Supprimer', 'Modifier', 'Ajouter', 'Rechercher', 'Filtrer', 'Exporter', 'Importer'],
                'es' => ['Enviar', 'Cancelar', 'Guardar', 'Eliminar', 'Editar', 'Agregar', 'Buscar', 'Filtrar', 'Exportar', 'Importar'],
            ],
            'label' => [
                'en' => ['Username', 'Email', 'Password', 'Confirm Password', 'First Name', 'Last Name', 'Phone', 'Address'],
                'fr' => ['Nom d\'utilisateur', 'Email', 'Mot de passe', 'Confirmer le mot de passe', 'Prénom', 'Nom de famille', 'Téléphone', 'Adresse'],
                'es' => ['Nombre de usuario', 'Correo electrónico', 'Contraseña', 'Confirmar contraseña', 'Nombre', 'Apellido', 'Teléfono', 'Dirección'],
            ],
            'message' => [
                'en' => ['Operation completed successfully', 'Please wait while we process your request', 'Your changes have been saved'],
                'fr' => ['Opération terminée avec succès', 'Veuillez patienter pendant que nous traitons votre demande', 'Vos modifications ont été enregistrées'],
                'es' => ['Operación completada exitosamente', 'Por favor espere mientras procesamos su solicitud', 'Sus cambios han sido guardados'],
            ],
            'title' => [
                'en' => ['Welcome', 'Dashboard', 'Settings', 'Profile', 'Users', 'Reports', 'Analytics'],
                'fr' => ['Bienvenue', 'Tableau de bord', 'Paramètres', 'Profil', 'Utilisateurs', 'Rapports', 'Analyses'],
                'es' => ['Bienvenido', 'Panel de control', 'Configuración', 'Perfil', 'Usuarios', 'Informes', 'Análisis'],
            ],
            'error' => [
                'en' => ['An error occurred', 'Invalid input', 'Access denied', 'Resource not found', 'Server error'],
                'fr' => ['Une erreur s\'est produite', 'Entrée invalide', 'Accès refusé', 'Ressource introuvable', 'Erreur du serveur'],
                'es' => ['Ocurrió un error', 'Entrada inválida', 'Acceso denegado', 'Recurso no encontrado', 'Error del servidor'],
            ],
            'success' => [
                'en' => ['Operation successful', 'Data saved', 'Changes applied', 'Record created', 'Update completed'],
                'fr' => ['Opération réussie', 'Données enregistrées', 'Modifications appliquées', 'Enregistrement créé', 'Mise à jour terminée'],
                'es' => ['Operación exitosa', 'Datos guardados', 'Cambios aplicados', 'Registro creado', 'Actualización completada'],
            ],
            'warning' => [
                'en' => ['Please be careful', 'This action cannot be undone', 'Are you sure?', 'Warning: Invalid data'],
                'fr' => ['Soyez prudent', 'Cette action ne peut pas être annulée', 'Êtes-vous sûr?', 'Attention: Données invalides'],
                'es' => ['Por favor tenga cuidado', 'Esta acción no se puede deshacer', '¿Está seguro?', 'Advertencia: Datos inválidos'],
            ],
            'info' => [
                'en' => ['Important information', 'Please note', 'Help text', 'Additional details', 'Instructions'],
                'fr' => ['Informations importantes', 'Veuillez noter', 'Texte d\'aide', 'Détails supplémentaires', 'Instructions'],
                'es' => ['Información importante', 'Tenga en cuenta', 'Texto de ayuda', 'Detalles adicionales', 'Instrucciones'],
            ],
            'placeholder' => [
                'en' => ['Enter your text here', 'Type to search', 'Select an option', 'Choose a date'],
                'fr' => ['Entrez votre texte ici', 'Tapez pour rechercher', 'Sélectionnez une option', 'Choisissez une date'],
                'es' => ['Ingrese su texto aquí', 'Escriba para buscar', 'Seleccione una opción', 'Elija una fecha'],
            ],
            'tooltip' => [
                'en' => ['Click to edit', 'Hover for more info', 'Help available', 'Additional options'],
                'fr' => ['Cliquez pour modifier', 'Survolez pour plus d\'infos', 'Aide disponible', 'Options supplémentaires'],
                'es' => ['Haga clic para editar', 'Pase el mouse para más info', 'Ayuda disponible', 'Opciones adicionales'],
            ],
        ];

        $locale = $this->faker->randomElement(['en', 'fr', 'es']);
        $contents = $contentMap[$prefix][$locale] ?? ['Sample content'];

        return $this->faker->randomElement($contents);
    }
}
