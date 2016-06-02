<?php

namespace tool_inactive_user_cleanup\task;
class tool_inactive_user_cleanup_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'tool_inactive_user_cleanup');
    }

    
    public function execute() {
        
    }//end of function execute()
}// End of class
