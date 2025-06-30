/**
 * Interface representing a Widget in the Encom dashboard
 */
interface Widget {
    id: number;
    name: string;
    type: string;
    position_x: number;
    position_y: number;
    width: number;
    height: number;
    settings: any;
    created_at: string;
    updated_at: string;
}

/**
 * Type for widget types supported by the system
 */
type WidgetType = 'weather' | 'bin-collection' | 'plant-watering';

/**
 * Interface for weather widget settings
 */
interface WeatherWidgetSettings {
    location: string;
    units: 'metric' | 'imperial';
    show_forecast: boolean;
    days_to_show: number;
    api_key?: string;
}

/**
 * Interface for bin collection widget settings
 */
interface BinCollectionWidgetSettings {
    bin_types: {
        name: string;
        color: string;
        collection_day: number; // 0-6 (Sunday-Saturday)
        collection_frequency: 'weekly' | 'biweekly' | 'monthly';
        next_collection_date?: string;
    }[];
    show_countdown: boolean;
}

/**
 * Interface for plant watering widget settings
 */
interface PlantWateringWidgetSettings {
    plants: {
        name: string;
        location: string;
        watering_frequency: number; // days
        last_watered: string;
        watering_conditions: {
            min_temperature?: number;
            max_temperature?: number;
            avoid_rain?: boolean;
        };
    }[];
    check_weather: boolean;
}

/**
 * Type guard to check if settings are for a weather widget
 */
function isWeatherWidgetSettings(settings: any): settings is WeatherWidgetSettings {
    return settings && 
           typeof settings.location === 'string' &&
           (settings.units === 'metric' || settings.units === 'imperial');
}

/**
 * Type guard to check if settings are for a bin collection widget
 */
function isBinCollectionWidgetSettings(settings: any): settings is BinCollectionWidgetSettings {
    return settings && 
           Array.isArray(settings.bin_types) &&
           typeof settings.show_countdown === 'boolean';
}

/**
 * Type guard to check if settings are for a plant watering widget
 */
function isPlantWateringWidgetSettings(settings: any): settings is PlantWateringWidgetSettings {
    return settings && 
           Array.isArray(settings.plants) &&
           typeof settings.check_weather === 'boolean';
}

export {
    Widget,
    WidgetType,
    WeatherWidgetSettings,
    BinCollectionWidgetSettings,
    PlantWateringWidgetSettings,
    isWeatherWidgetSettings,
    isBinCollectionWidgetSettings,
    isPlantWateringWidgetSettings
};