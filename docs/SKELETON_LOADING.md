# Skeleton loading en pantallas con datos dinámicos

La app usa el componente **`Skeleton`** (`resources/js/Components/Skeleton.vue`) para mostrar placeholders animados (gris/blanco) mientras se “cargan” los datos, mejorando la sensación de velocidad.

## Dónde está implementado

- **Dashboard** – Tarjetas “Puntos por hijo”
- **Historial** (`History/Index.vue`) – Tabla de movimientos
- **Canje de puntos** (`Redemptions/Create.vue`) – Formulario
- **Acciones** (`Actions/Index.vue`) – Tabla de acciones
- **Invitar a otro padre** (`Invitations/Index.vue`) – Formulario y lista
- **Onboarding / Hijos** (`Onboarding.vue`) – Lista de hijos y formulario
- **Ficha pública del hijo** (`ChildCard/Show.vue`) – Cabecera e historial

## Cómo usar en nuevas pantallas

### 1. Importar el componente

```vue
import Skeleton from '@/Components/Skeleton.vue';
```

### 2. Estado de “contenido listo”

```vue
import { onMounted, ref } from 'vue';

const showContent = ref(false);
onMounted(() => {
    requestAnimationFrame(() => {
        setTimeout(() => {
            showContent.value = true;
        }, 220);
    });
});
```

(Con Inertia los datos suelen llegar en el primer request; el retraso corto hace que se vea el skeleton y luego el contenido real.)

### 3. En el template

- Envuelve el **bloque que depende de datos** (tabla, lista, formulario) en:
  - `v-if="!showContent"` → contenido skeleton (misma estructura visual, con `<Skeleton>`)
  - `v-else` → contenido real (tus datos).
- Usa **variantes** del componente:
  - `variant="line"` – Líneas de texto (por defecto altura `h-4`; puedes pasar clase para tamaño).
  - `variant="block"` – Bloques rectangulares.
  - `variant="circle"` – Círculos (avatares, iconos).
- Tamaño con **clases** (Tailwind): por ejemplo `class="h-4 w-32"`, `class="h-14 w-14 shrink-0"`.

### Ejemplo mínimo (lista)

```vue
<template v-if="!showContent">
    <div v-for="n in 5" :key="'sk-' + n" class="flex gap-3 p-3">
        <Skeleton variant="circle" class="h-10 w-10 shrink-0" />
        <div class="flex-1 space-y-1">
            <Skeleton variant="line" class="w-32" />
            <Skeleton variant="line" class="w-24" />
        </div>
    </div>
</template>
<template v-else>
    <div v-for="item in items" :key="item.id">...</div>
</template>
```

### Ejemplo tabla

```vue
<tbody>
    <template v-if="!showContent">
        <tr v-for="n in 8" :key="'sk-' + n">
            <td class="px-4 py-3"><Skeleton variant="line" class="w-24" /></td>
            <td class="px-4 py-3"><Skeleton variant="line" class="w-32" /></td>
        </tr>
    </template>
    <tr v-else v-for="row in rows" :key="row.id">...</tr>
</tbody>
```

## Regla para nuevas funciones

Al añadir una **nueva pantalla que muestre datos de consultas a BD o API**:

1. Importar `Skeleton` y usar el patrón `showContent` + `onMounted` (retraso ~220 ms).
2. Diseñar el skeleton para que imite la estructura de la pantalla (mismo número de columnas, líneas, avatares, etc.).
3. Mantener el mismo archivo de componente: `resources/js/Components/Skeleton.vue` (variantes `line`, `block`, `circle`; prop opcional `skeleton-class` para clases extra).
