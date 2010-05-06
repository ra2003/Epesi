/*
 * Archive plugin script
 * @version @package_version@
 */

function rcmail_epesi_archive(prop)
{
  if (!rcmail.env.uid && (!rcmail.message_list || !rcmail.message_list.get_selection().length))
    return;

  var uids = rcmail.env.uid ? rcmail.env.uid : rcmail.message_list.get_selection().join(',');

  rcmail.set_busy(true, 'loading');
  rcmail.http_post('plugin.epesi_archive', '_uid='+uids+'&_mbox='+urlencode(rcmail.env.mailbox), true);
}

// callback for app-onload event
if (window.rcmail) {
  rcmail.addEventListener('init', function(evt) {

    // register command (directly enable in message view mode)
    rcmail.register_command('plugin.epesi_archive', rcmail_epesi_archive, (rcmail.env.uid && rcmail.env.mailbox != rcmail.env.archive_mailbox && rcmail.env.mailbox != rcmail.env.drafts_mailbox));

    // add event-listener to message list
    if (rcmail.message_list)
      rcmail.message_list.addEventListener('select', function(list){
        rcmail.enable_command('plugin.epesi_archive', (list.get_selection().length > 0 && rcmail.env.mailbox != rcmail.env.drafts_mailbox && rcmail.env.mailbox != rcmail.env.archive_mailbox));
      });

    // set css style for archive folder
    var li;
    if (rcmail.env.archive_mailbox && rcmail.env.archive_mailbox_icon && (li = rcmail.get_folder_li(rcmail.env.archive_mailbox)))
      $(li).css('background-image', 'url(' + rcmail.env.archive_mailbox_icon + ')');

  })
}

