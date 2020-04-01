/*global $, dotclear, getData */
'use strict';

dotclear.dmPendingPostsCount = function() {
  $.get('services.php', {
      f: 'dmPendingPostsCount',
      xd_check: dotclear.nonce,
    })
    .done(function(data) {
      if ($('rsp[status=failed]', data).length > 0) {
        // For debugging purpose only:
        // console.log($('rsp',data).attr('message'));
        window.console.log('Dotclear REST server error');
      } else {
        const ret = $('rsp>count', data).attr('ret');
        if (ret != dotclear.dbPendingPostsCount_Counter) {
          // First pass or counter changed
          let icon = $('#dashboard-main #icons p a[href="posts.php?status=-2"]');
          if (icon.length) {
            // Update count if exists
            const nb_label = icon.children('span.db-icon-title-dm-pending');
            if (nb_label.length) {
              nb_label.text(ret);
            }
          } else {
            if (ret != '') {
              // Add full element (link + counter)
              icon = $('#dashboard-main #icons p a[href="posts.php"]');
              if (icon.length) {
                const xml = ' <a href="posts.php?status=-2"><span class="db-icon-title-dm-pending">' + ret + '</span></a>';
                icon.after(xml);
              }
            }
          }
          const nb = $('rsp>count', data).attr('nb');
          // Badge on module
          dotclear.badge(
            $('#pending-posts'), {
              id: 'dmpp',
              value: nb,
              remove: (nb == 0),
              type: 'soft',
            }
          );
          // Store current counter
          dotclear.dbPendingPostsCount_Counter = ret;
        }
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);
    })
    .always(function() {
      // Nothing here
    });
};

dotclear.dmPendingPostsView = function(line, action, e) {
  action = action || 'toggle';
  if ($(line).attr('id') == undefined) {
    return;
  }

  const postId = $(line).attr('id').substr(4);
  const lineId = `dmppe${postId}`;
  let li = document.getElementById(lineId);

  if (!li) {
    // Get content
    dotclear.getEntryContent(postId, function(content) {
      if (content) {
        li = document.createElement('li');
        li.id = lineId;
        li.className = 'expand';
        $(li).append(content);
        $(line).addClass('expand');
        line.parentNode.insertBefore(li, line.nextSibling);
      } else {
        $(line).toggleClass('expand');
      }
    }, {
      clean: (e.metaKey),
      length: 300
    });
  } else {
    $(li).toggle();
    $(line).toggleClass('expand');
  }
};

dotclear.dmPendingCommentsCount = function() {
  $.get('services.php', {
      f: 'dmPendingCommentsCount',
      xd_check: dotclear.nonce,
    })
    .done(function(data) {
      if ($('rsp[status=failed]', data).length > 0) {
        // For debugging purpose only:
        // console.log($('rsp',data).attr('message'));
        window.console.log('Dotclear REST server error');
      } else {
        const ret = $('rsp>count', data).attr('ret');
        if (ret != dotclear.dbPendingCommentsCount_Counter) {
          // First pass or counter changed
          let icon = $('#dashboard-main #icons p a[href="comments.php?status=-1"]');
          if (icon.length) {
            // Update count if exists
            const nb_label = icon.children('span.db-icon-title-dm-pending');
            if (nb_label.length) {
              nb_label.text(ret);
            }
          } else {
            if (ret != '') {
              // Add full element (link + counter)
              icon = $('#dashboard-main #icons p a[href="comments.php"]');
              if (icon.length) {
                const xml = ' <a href="comments.php?status=-1"><span class="db-icon-title-dm-pending">' + ret + '</span></a>';
                icon.after(xml);
              }
            }
          }
          const nb = $('rsp>count', data).attr('nb');
          // Badge on module
          dotclear.badge(
            $('#pending-comments'), {
              id: 'dmpc',
              value: nb,
              remove: (nb == 0),
              type: 'soft',
            }
          );
          // Store current counter
          dotclear.dbPendingCommentsCount_Counter = ret;
        }
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);
    })
    .always(function() {
      // Nothing here
    });
};

dotclear.dmPendingCommentsView = function(line, action, e) {
  action = action || 'toggle';

  const commentId = $(line).attr('id').substr(4);
  const lineId = `dmpce${commentId}`;
  let li = document.getElementById(lineId);

  // If meta key down or it's a spam then display content HTML code
  const clean = (e.metaKey || $(line).hasClass('sts-junk'));

  if (!li) {
    // Get content
    dotclear.getCommentContent(commentId, function(content) {
      if (content) {
        li = document.createElement('li');
        li.id = lineId;
        li.className = 'expand';
        $(li).append(content);
        $(line).addClass('expand');
        line.parentNode.insertBefore(li, line.nextSibling);

      } else {
        $(line).removeClass('expand');
      }
    }, {
      metadata: false,
      clean: clean
    });
  } else {
    $(li).toggle();
    $(line).toggleClass('expand');
  }
};

$(function() {
  Object.assign(dotclear, getData('dm_pending'));
  $.expandContent({
    lines: $('#pending-posts li.line'),
    callback: dotclear.dmPendingPostsView
  });
  $('#pending-posts ul').addClass('expandable');
  if (dotclear.dmPendingPosts_Counter) {
    const icon = $('#dashboard-main #icons p a[href="posts.php"]');
    if (icon.length) {
      // Icon exists on dashboard
      // First pass
      dotclear.dmPendingPostsCount();
      // Then fired every 60 seconds
      dotclear.dbPendingPostsCount_Timer = setInterval(dotclear.dmPendingPostsCount, 60 * 1000);
    }
  }
  $.expandContent({
    lines: $('#pending-comments li.line'),
    callback: dotclear.dmPendingCommentsView
  });
  $('#pending-comments ul').addClass('expandable');
  if (dotclear.dmPendingComments_Counter) {
    const icon = $('#dashboard-main #icons p a[href="comments.php"]');
    if (icon.length) {
      // Icon exists on dashboard
      // First pass
      dotclear.dmPendingCommentsCount();
      // Then fired every 60 seconds
      dotclear.dbPendingCommentsCount_Timer = setInterval(dotclear.dmPendingCommentsCount, 60 * 1000);
    }
  }
});
