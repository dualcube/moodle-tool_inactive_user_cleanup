// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Standalone Grunt configuration for tool_inactive_user_cleanup.
 *
 * This plugin currently has no amd/src or scss sources - the amd task below
 * simply has nothing to do until some are added. It exists so this plugin
 * can be linted/built on its own, without checking out a full Moodle site.
 *
 * Note: moodle-plugin-ci's own "grunt" CI command DOES pick up this file
 * once it exists (it runs Grunt with this plugin's directory as the working
 * directory whenever a plugin-local Gruntfile.js is present, and always does
 * so for its "stylelint" task regardless). The stylelint/yui/gherkinlint
 * tasks below are registered as harmless no-ops purely so that CI invocation
 * degrades gracefully rather than failing with "Task not found" - our real
 * CI workflow passes --no-plugin-node during install specifically so it
 * never needs to reach that fallback in the first place.
 *
 * @copyright  DualCube (https://dualcube.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        eslint: {
            amd: {
                src: ['amd/src/**/*.js'],
            },
        },

        uglify: {
            amd: {
                files: [{
                    expand: true,
                    cwd: 'amd/src',
                    src: ['**/*.js'],
                    dest: 'amd/build',
                    ext: '.min.js',
                }],
            },
        },

        watch: {
            amd: {
                files: ['amd/src/**/*.js'],
                tasks: ['eslint', 'uglify'],
            },
        },
    });

    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('amd', ['eslint', 'uglify']);
    grunt.registerTask('default', ['amd']);

    // No-ops: this plugin has no stylesheets, YUI modules, or Behat features
    // for these tasks to act on. Registered so moodle-plugin-ci's grunt
    // command never hard-fails with "Task not found" if it ever reaches them.
    grunt.registerTask('stylelint', []);
    grunt.registerTask('yui', []);
    grunt.registerTask('gherkinlint', []);
};
