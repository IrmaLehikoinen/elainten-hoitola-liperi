@php
    $rawBlocks = old('blocks', null);

    if ($rawBlocks === null) {
        $rawBlocks = collect($course->content_blocks ?? [])->map(function ($block, $index) {
            $block['uid'] = 'existing'.$index;
            if (($block['type'] ?? null) === 'checklist') {
                $block['items_text'] = implode("\n", $block['items'] ?? []);
            }
            return $block;
        })->values()->all();
    }
@endphp

<div x-data="{
    presentationType: '{{ old('presentation_type', $course->presentation_type ?? 'none') }}',
    blocks: {{ \Illuminate\Support\Js::from($rawBlocks) }},
    uidCounter: 1,
    addBlock(type) {
        this.blocks.push({ uid: 'new' + (this.uidCounter++), type: type, text: '', align: 'left', items_text: '', path: null });
    },
    removeBlock(i) { this.blocks.splice(i, 1); },
    moveUp(i) { if (i > 0) { const b = this.blocks; [b[i-1], b[i]] = [b[i], b[i-1]]; } },
    moveDown(i) { if (i < this.blocks.length - 1) { const b = this.blocks; [b[i+1], b[i]] = [b[i], b[i+1]]; } }
}">
    <form
        method="POST"
        action="{{ $course->exists ? route('kurssit.courses.update', $course) : route('kurssit.courses.store') }}"
        enctype="multipart/form-data"
        class="mt-2 space-y-5"
    >
        @csrf
        @if ($course->exists)
            @method('PATCH')
        @endif

        <div>
            <label class="block text-sm font-medium">Kurssin nimi</label>
            <input type="text" name="name" value="{{ old('name', $course->name) }}" required class="mt-1 w-full rounded-md border-gray-300">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Lyhyt esittely</label>
            <textarea name="short_description" rows="3" required
                class="mt-1 w-full rounded-md border-gray-300">{{ old('short_description', $course->short_description) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Näkyy aina kurssilistassa ja ilmoittautumissivun yläosassa.</p>
            @error('short_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Ajankohta</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $course->starts_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium">Hinta (€)</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $course->price) }}" class="mt-1 w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium">Paikkamäärä</label>
                <input type="number" min="0" name="max_participants" value="{{ old('max_participants', $course->max_participants) }}" required class="mt-1 w-full rounded-md border-gray-300">
                @error('max_participants') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Tarkempi sisältö</label>
            <div class="mt-2 flex flex-wrap gap-4">
                <label class="flex items-center gap-2">
                    <input type="radio" name="presentation_type" value="none" x-model="presentationType">
                    Ei lisäsisältöä
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="presentation_type" value="brochure" x-model="presentationType">
                    Lataa valmis esite
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="presentation_type" value="blocks" x-model="presentationType">
                    Rakenna esittelysivu
                </label>
            </div>
        </div>

        <div x-show="presentationType === 'brochure'">
            <label class="block text-sm font-medium">Esite (PDF tai kuva)</label>
            <input type="file" name="brochure" accept=".pdf,.jpg,.jpeg,.png,.heic,.heif" class="mt-1 w-full">
            @if ($course->brochure_path)
                <p class="mt-1 text-sm text-gray-500">Nykyinen esite: {{ basename($course->brochure_path) }} (lataa uusi korvataksesi)</p>
            @endif
            @error('brochure') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div x-show="presentationType === 'blocks'" class="space-y-4 rounded-md border border-gray-200 p-4">
            <template x-for="(block, index) in blocks" :key="block.uid">
                <div class="rounded-md border bg-gray-50 p-3 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase text-gray-500" x-text="({
                            heading: 'Pääotsikko',
                            subheading: 'Alaotsikko',
                            paragraph: 'Leipäteksti',
                            image_full: 'Koko levyinen kuva',
                            image_side: 'Pienempi kuva + teksti',
                            checklist: 'Väkäslista'
                        })[block.type]"></span>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="moveUp(index)" class="text-xs text-gray-500 hover:text-gray-800">↑</button>
                            <button type="button" @click="moveDown(index)" class="text-xs text-gray-500 hover:text-gray-800">↓</button>
                            <button type="button" @click="removeBlock(index)" class="text-xs text-red-600 hover:text-red-800">Poista</button>
                        </div>
                    </div>

                    <input type="hidden" :name="'blocks[' + index + '][uid]'" :value="block.uid">
                    <input type="hidden" :name="'blocks[' + index + '][type]'" :value="block.type">

                    <template x-if="block.type === 'heading' || block.type === 'subheading' || block.type === 'paragraph'">
                        <textarea :name="'blocks[' + index + '][text]'" x-model="block.text" rows="2"
                            class="w-full rounded-md border-gray-300 text-sm" placeholder="Teksti tähän..."></textarea>
                    </template>

                    <template x-if="block.type === 'image_full' || block.type === 'image_side'">
                        <div class="space-y-2">
                            <template x-if="block.path">
                                <p class="text-xs text-gray-500">Nykyinen kuva tallennettu — valitse uusi tiedosto korvataksesi.</p>
                            </template>
                            <input type="hidden" :name="'blocks[' + index + '][path]'" :value="block.path">
                            <input type="file" accept=".jpg,.jpeg,.png,.heic,.heif" :name="'block_image_' + block.uid" class="text-sm">

                            <template x-if="block.type === 'image_side'">
                                <div class="space-y-2">
                                    <select :name="'blocks[' + index + '][align]'" x-model="block.align" class="rounded-md border-gray-300 text-sm">
                                        <option value="left">Kuva vasemmalla</option>
                                        <option value="right">Kuva oikealla</option>
                                    </select>
                                    <textarea :name="'blocks[' + index + '][text]'" x-model="block.text" rows="3"
                                        class="w-full rounded-md border-gray-300 text-sm" placeholder="Kuvan vieressä oleva teksti..."></textarea>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="block.type === 'checklist'">
                        <textarea :name="'blocks[' + index + '][items_text]'" x-model="block.items_text" rows="4"
                            class="w-full rounded-md border-gray-300 text-sm" placeholder="Yksi kohta per rivi..."></textarea>
                    </template>
                </div>
            </template>

            <div class="flex flex-wrap gap-2 pt-2">
                <button type="button" @click="addBlock('heading')" class="rounded-md border px-3 py-1.5 text-xs">+ Pääotsikko</button>
                <button type="button" @click="addBlock('subheading')" class="rounded-md border px-3 py-1.5 text-xs">+ Alaotsikko</button>
                <button type="button" @click="addBlock('paragraph')" class="rounded-md border px-3 py-1.5 text-xs">+ Leipäteksti</button>
                <button type="button" @click="addBlock('image_full')" class="rounded-md border px-3 py-1.5 text-xs">+ Koko levyinen kuva</button>
                <button type="button" @click="addBlock('image_side')" class="rounded-md border px-3 py-1.5 text-xs">+ Pieni kuva + teksti</button>
                <button type="button" @click="addBlock('checklist')" class="rounded-md border px-3 py-1.5 text-xs">+ Väkäslista</button>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-brand rounded-md px-4 py-2 text-sm font-medium">Tallenna</button>
            @if ($course->exists)
                <button type="button" onclick="window.location.href='{{ route('kurssit.courses.index') }}'" class="rounded-md border px-4 py-2 text-sm">Peruuta</button>
            @endif
        </div>
    </form>
</div>