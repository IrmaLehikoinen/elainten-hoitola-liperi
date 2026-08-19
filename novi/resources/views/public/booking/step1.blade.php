<x-layouts.public :company="$company">
    <div x-data="{
        animalCount: 1,
        animals: [{ species: 'koira' }],
        updateCount() {
            const count = Number(this.animalCount);
            while (this.animals.length < count) { this.animals.push({ species: 'koira' }); }
            while (this.animals.length > count) { this.animals.pop(); }
        }
    }">
        <h2 class="text-lg font-semibold" style="color: var(--brand-text);">
            1. Kerro lemmikeistäsi ja hoidon kestosta
        </h2>

        <form method="POST" action="{{ route('public.booking.availability') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium">Eläinten määrä</label>
                <select x-model.number="animalCount" @change="updateCount" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    <option :value="1">1 eläin</option>
                    <option :value="2">2 eläintä</option>
                    <option :value="3">3 eläintä</option>
                    <option :value="4">4 eläintä</option>
                    <option :value="5">5 eläintä</option>
                </select>
            </div>

            <div class="space-y-3">
                <template x-for="(animal, index) in animals" :key="index">
                    <div>
                        <label class="block text-sm font-medium" x-text="'Eläin ' + (index + 1)"></label>
                        <select x-model="animal.species" :name="'animals[' + index + '][species]'" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            <option value="koira">Koira</option>
                            <option value="kissa">Kissa</option>
                            <option value="kani">Kani</option>
                            <option value="muu">Muu eläin</option>
                        </select>
                    </div>
                </template>
            </div>

            <div>
                <label class="block text-sm font-medium">Hoitomuoto</label>
                <select name="care_type" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    @foreach ($careTypes as $careType)
                        <option value="{{ $careType->slug }}">{{ $careType->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Hoidon pituus</label>
                    <input type="number" name="duration_amount" min="1" value="1" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium">Yksikkö</label>
                    <select name="duration_unit" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="days">Päivää</option>
                        <option value="weeks">Viikkoa</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-brand w-full rounded-md px-4 py-3 text-sm font-semibold">
                Näytä vapaat ajat
            </button>
        </form>
    </div>
</x-layouts.public>