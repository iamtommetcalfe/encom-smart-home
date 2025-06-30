# Getting Started with Encom Smart Home Web App

This document provides a quick overview of what has been accomplished and the next steps to get started with the Encom smart home web app.

## What's Been Accomplished

1. **Implementation Plan**: A comprehensive implementation plan has been created in `encom-implementation-plan.md` that outlines the steps to build the Encom smart home web app.

2. **Setup Script**: A setup script (`setup-encom.sh`) has been created to automate the initial setup of the Laravel project with TypeScript and Vue.js.

3. **Widget Templates**: Templates for the widget system have been created in the `widget-templates` directory:
   - `Widget.php`: Laravel model for widgets
   - `create_widgets_table.php`: Migration for the widgets table
   - `WidgetController.php`: Controller for widget CRUD operations
   - `Widget.ts`: TypeScript interfaces for widgets

4. **Documentation**: A README file has been created with instructions on how to use the templates and set up the project.

## Next Steps

To get started with the Encom smart home web app, follow these steps:

1. **Run the Setup Script**:
   ```bash
   chmod +x setup-encom.sh
   ./setup-encom.sh
   ```
   This will create a new Laravel project with TypeScript and Vue.js.

2. **Copy Widget Templates**:
   After the setup script completes, copy the widget templates to the appropriate locations in the new project:
   ```bash
   cp widget-templates/Widget.php app/Models/
   cp widget-templates/create_widgets_table.php database/migrations/$(date +%Y_%m_%d_000000)_create_widgets_table.php
   cp widget-templates/WidgetController.php app/Http/Controllers/
   mkdir -p resources/js/types
   cp widget-templates/Widget.ts resources/js/types/
   ```

3. **Set Up Routes**:
   Add the following routes to `routes/web.php`:
   ```php
   use App\Http\Controllers\WidgetController;

   Route::resource('widgets', WidgetController::class);
   Route::patch('widgets/{widget}/position', [WidgetController::class, 'updatePosition'])->name('widgets.update-position');
   Route::patch('widgets/{widget}/size', [WidgetController::class, 'updateSize'])->name('widgets.update-size');
   ```

4. **Create Widget Views**:
   Create the necessary views for the widget system:
   ```bash
   mkdir -p resources/views/widgets
   ```

   You'll need to create the following views:
   - `index.blade.php`: List all widgets
   - `create.blade.php`: Form to create a new widget
   - `edit.blade.php`: Form to edit an existing widget
   - `show.blade.php`: Display a single widget

5. **Create Widget Components**:
   Create Vue components for each widget type:
   ```bash
   mkdir -p resources/js/widgets
   ```

   You'll need to create the following components:
   - `WeatherWidget.vue`: Weather widget component
   - `BinCollectionWidget.vue`: Bin collection widget component
   - `PlantWateringWidget.vue`: Plant watering widget component

6. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

7. **Start Development**:
   ```bash
   php artisan serve
   ```
   In a separate terminal:
   ```bash
   npm run dev
   ```

## Styling Template

A modern, responsive styling template has been added to the project using Tailwind CSS. The template includes:

1. **Layout Structure**: A responsive layout with header, main content area, and footer.
   - Located in `resources/views/layouts/app.blade.php`
   - Includes navigation links, dark mode toggle, and mobile responsiveness

2. **Dashboard Design**: A grid-based dashboard for displaying widgets.
   - Located in `resources/views/welcome.blade.php`
   - Uses a responsive grid system (1 column on mobile, 2 on tablet, 3 on desktop)
   - Includes placeholder widgets for weather, bin collection, and plant watering

3. **Dark Mode Support**: Toggle between light and dark modes.
   - Implemented using Tailwind's dark mode with class strategy
   - Persists user preference using localStorage

4. **Custom Color Scheme**: A custom color palette defined in `tailwind.config.js`.
   - Primary: Blue shades for main actions and highlights
   - Secondary: Purple shades for secondary elements
   - Success: Green shades for success states
   - Warning: Orange shades for warning states
   - Danger: Red shades for error states
   - Dark: Slate shades for text and backgrounds

### Using the Styling Template

To create new pages or components that match the styling template:

1. **Extend the Layout**: Use Blade's `@extends` directive to use the app layout.
   ```php
   @extends('layouts.app')

   @section('content')
       <!-- Your content here -->
   @endsection
   ```

2. **Use Tailwind Classes**: Apply Tailwind utility classes for styling.
   - For dark mode support, use the `dark:` prefix (e.g., `text-dark-900 dark:text-dark-100`)
   - Use the custom color scheme (e.g., `bg-primary-500`, `text-success-600`)

3. **Create Widget Components**: Follow the widget examples in the dashboard for consistency.
   - Use card-based design with header and content sections
   - Maintain consistent spacing and typography

## Development Workflow

1. **Follow the Implementation Plan**: Refer to `encom-implementation-plan.md` for a detailed plan on how to implement the Encom smart home web app.

2. **Implement Features Incrementally**: Start with the core widget system, then implement each widget type one by one.

3. **Test Regularly**: Test each feature as you implement it to ensure it works as expected.

4. **Deploy to Internal Network**: Once the app is ready, deploy it to your internal network for use in your smart home.

## Resources

- Laravel Documentation: https://laravel.com/docs
- Vue.js Documentation: https://vuejs.org/guide/introduction.html
- TypeScript Documentation: https://www.typescriptlang.org/docs/

## Support

If you encounter any issues or have questions, please refer to the documentation or seek help from the Laravel, Vue.js, or TypeScript communities.

Happy coding!
