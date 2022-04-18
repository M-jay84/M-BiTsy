<center>
<a href="<?php echo URLROOT; ?>/mailbox/overview"><button type="button" class="<?php echo activelink('messages/overview'); ?>"><?php echo Lang::T("Over View"); ?></button></a>&nbsp;
<a href="<?php echo URLROOT; ?>/mailbox?type=inbox"><button type="button" class="<?php echo activelink('inbox', 'inbox'); ?>"><?php echo Lang::T("INBOX"); ?></button></a>&nbsp;
<a href="<?php echo URLROOT; ?>/mailbox?type=outbox"><button type="button" class="<?php echo activelink('outbox', 'outbox'); ?>"><?php echo Lang::T("OUTBOX"); ?></button></a>&nbsp;
<a href="<?php echo URLROOT; ?>/mailbox?type=draft"><button type="button" class="<?php echo activelink('draft', 'draft'); ?>"><?php echo Lang::T("DRAFT"); ?></button></a>&nbsp;
<a href="<?php echo URLROOT; ?>/mailbox?type=templates"><button type="button" class="<?php echo activelink('templates','templates' ); ?>"><?php echo Lang::T("TEMPLATES"); ?></button></a>&nbsp;
<a href="<?php echo URLROOT; ?>/message/create"><button type="button" class="<?php echo activelink('messages/create'); ?>"><?php echo Lang::T("COMPOSE"); ?></button></a>
</center><br>