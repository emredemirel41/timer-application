<template>
    <v-card>
        <v-layout>
            <v-app-bar :elevation="2" title="Application Bar"></v-app-bar>

            <v-navigation-drawer>
                <v-list>
                    <v-list-item title="Navigation drawer"></v-list-item>
                </v-list>
            </v-navigation-drawer>

            <v-main style="min-height: 300px;">
                <slot></slot>
            </v-main>
        </v-layout>
    </v-card>
</template>