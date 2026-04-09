<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Only display professional alert in development mode to avoid leaking paths to clients
if (ENVIRONMENT === 'development'):
?>
<div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px 16px; margin: 24px 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; word-wrap: break-word; overflow-wrap: break-word;">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <div style="background-color: #fee2e2; color: #ef4444; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <div style="flex-grow: 1; min-width: 0;">
            <h4 style="margin: 0 0 8px; color: #991b1b; font-size: 15px; font-weight: 700;">A PHP Error was encountered</h4>
            <div style="color: #b91c1c; font-size: 13px; line-height: 1.5; word-break: break-word;">
                <p style="margin: 0 0 4px;"><strong>Severity:</strong> <?php echo $severity; ?></p>
                <p style="margin: 0 0 4px;"><strong>Message:</strong> <?php echo $message; ?></p>
                <p style="margin: 0 0 4px;"><strong>Filename:</strong> <?php echo $filepath; ?></p>
                <p style="margin: 0 0 12px;"><strong>Line Number:</strong> <?php echo $line; ?></p>

                <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
                    <p style="margin: 16px 0 8px; font-weight: 700; color: #991b1b;">Debug Backtrace:</p>
                    <div style="background: rgba(255,255,255,0.5); border-radius: 8px; padding: 12px; font-family: monospace; font-size: 12px; color: #4b5563;">
                        <?php foreach (debug_backtrace() as $error): ?>
                            <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
                                <div style="margin-bottom: 8px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 4px;">
                                    File: <?php echo $error['file'] ?><br />
                                    Line: <?php echo $error['line'] ?><br />
                                    Function: <?php echo $error['function'] ?>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?php 
else:
	// In non-development mode, log the error but don't show anything to the user to keep the UI clean
	log_message('error', "PHP Error: $message in $filepath on line $line");
endif; 
?>