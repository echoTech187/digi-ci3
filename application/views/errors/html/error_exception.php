<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Only display professional alert in development mode
if (ENVIRONMENT === 'development'):
?>
<div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 24px; margin: 24px 0; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
    <div style="display: flex; align-items: flex-start; gap: 16px;">
        <div style="background-color: #fee2e2; color: #ef4444; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <div style="flex-grow: 1;">
            <h4 style="margin: 0 0 8px; color: #991b1b; font-size: 16px; font-weight: 700;">An uncaught Exception was encountered</h4>
            <div style="color: #b91c1c; font-size: 14px; line-height: 1.5;">
                <p style="margin: 0 0 4px;"><strong>Type:</strong> <?php echo get_class($exception); ?></p>
                <p style="margin: 0 0 4px;"><strong>Message:</strong> <?php echo $message; ?></p>
                <p style="margin: 0 0 4px;"><strong>Filename:</strong> <?php echo $exception->getFile(); ?></p>
                <p style="margin: 0 0 12px;"><strong>Line Number:</strong> <?php echo $exception->getLine(); ?></p>

                <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
                    <p style="margin: 16px 0 8px; font-weight: 700; color: #991b1b;">Debug Backtrace:</p>
                    <div style="background: rgba(255,255,255,0.5); border-radius: 8px; padding: 12px; font-family: monospace; font-size: 12px; color: #4b5563;">
                        <?php foreach ($exception->getTrace() as $error): ?>
                            <?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
                                <div style="margin-bottom: 8px; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 4px;">
                                    File: <?php echo $error['file']; ?><br />
                                    Line: <?php echo $error['line']; ?><br />
                                    Function: <?php echo $error['function']; ?>
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
	// In non-development mode, log the exception and show the general error page
	log_message('error', "Exception: $message in ".$exception->getFile());
	// CI will usually trigger error_general or similar for terminal exceptions
endif; 
?>