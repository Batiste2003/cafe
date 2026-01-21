<script setup>
import { Primitive } from "reka-ui";
import { cn } from "@/lib/utils";
import { buttonVariants } from ".";

const props = defineProps();
</script>

<template>
  <Primitive
    :as="as"
    :as-child="asChild"
    :class="cn(buttonVariants({ variant, size }), props.class)"
  >
    <slot />
  </Primitive>
</template>
