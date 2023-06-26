/*global $, dotclear */
'use strict';

dotclear.dmPendingPostsCount = (icon) => {
  dotclear.services(
    'dmPendingPostsCount',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret) {
            const { msg } = response.payload;
            if (msg !== undefined && msg != dotclear.dbPendingPostsCount_Counter) {
              const href = icon.attr('href');
              const param = `${href.includes('?') ? '&' : '?'}status=-2`;
              const url = `${href}${param}`;
              // First pass or counter changed
              const link = $(`#dashboard-main #icons p a[href="${url}"]`);
              if (link.length) {
                // Update count if exists
                const nb_label = icon.children('span.db-icon-title-dm-pending');
                if (nb_label.length) {
                  nb_label.text(msg);
                }
              } else if (msg != '' && icon.length) {
                // Add full element (link + counter)
                const xml = ` <a href="${url}"><span class="db-icon-title-dm-pending">${msg}</span></a>`;
                icon.after(xml);
              }
              const { nb } = response.payload;
              // Badge on module
              dotclear.badge($('#pending-posts'), {
                id: 'dmpp',
                value: nb,
                remove: nb == 0,
                type: 'soft',
              });
              // Store current counter
              dotclear.dbPendingPostsCount_Counter = msg;
            }
          }
        } else {
          console.log(dotclear.debug && response?.message ? response.message : 'Dotclear REST server error');
          return;
        }
      } catch (e) {
        console.log(e);
      }
    },
    (error) => {
      console.log(error);
    },
    true, // Use GET method
    { json: 1 },
  );
};

dotclear.dmPendingPostsView = (line, action = 'toggle', e = null) => {
  if ($(line).attr('id') == undefined) {
    return;
  }

  const postId = $(line).attr('id').substr(4);
  const lineId = `dmppe${postId}`;
  let li = document.getElementById(lineId);

  if (li) {
    $(li).toggle();
    $(line).toggleClass('expand');
  } else {
    // Get content
    dotclear.getEntryContent(
      postId,
      (content) => {
        if (content) {
          li = document.createElement('li');
          li.id = lineId;
          li.className = 'expand';
          $(li).append(content);
          $(line).addClass('expand');
          line.parentNode.insertBefore(li, line.nextSibling);
          return;
        }
        $(line).toggleClass('expand');
      },
      {
        clean: e.metaKey,
        length: 300,
      },
    );
  }
};

dotclear.dmPendingCommentsCount = (icon) => {
  dotclear.services(
    'dmPendingCommentsCount',
    (data) => {
      try {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret) {
            const { msg } = response.payload;
            if (msg !== undefined && msg != dotclear.dbPendingCommentsCount_Counter) {
              const href = icon.attr('href');
              const param = `${href.includes('?') ? '&' : '?'}status=-1`;
              const url = `${href}${param}`;
              // First pass or counter changed
              const link = $(`#dashboard-main #icons p a[href="${url}"]`);
              if (link.length) {
                // Update count if exists
                const nb_label = icon.children('span.db-icon-title-dm-pending');
                if (nb_label.length) {
                  nb_label.text(msg);
                }
              } else if (msg != '') {
                // Add full element (link + counter)
                const xml = ` <a href="${url}"><span class="db-icon-title-dm-pending">${msg}</span></a>`;
                icon.after(xml);
              }
              const { nb } = response.payload;
              // Badge on module
              dotclear.badge($('#pending-comments'), {
                id: 'dmpc',
                value: nb,
                remove: nb == 0,
                type: 'soft',
              });
              // Store current counter
              dotclear.dbPendingCommentsCount_Counter = msg;
            }
          }
        } else {
          console.log(dotclear.debug && response?.message ? response.message : 'Dotclear REST server error');
          return;
        }
      } catch (e) {
        console.log(e);
      }
    },
    (error) => {
      console.log(error);
    },
    true, // Use GET method
    { json: 1 },
  );
};

dotclear.dmPendingCommentsView = (line, action = 'toggle', e = null) => {
  const commentId = $(line).attr('id').substr(4);
  const lineId = `dmpce${commentId}`;
  let li = document.getElementById(lineId);

  // If meta key down or it's a spam then display content HTML code
  const clean = e.metaKey || $(line).hasClass('sts-junk');

  if (li) {
    $(li).toggle();
    $(line).toggleClass('expand');
  } else {
    // Get content
    dotclear.getCommentContent(
      commentId,
      (content) => {
        if (content) {
          li = document.createElement('li');
          li.id = lineId;
          li.className = 'expand';
          $(li).append(content);
          $(line).addClass('expand');
          line.parentNode.insertBefore(li, line.nextSibling);
          return;
        }
        $(line).removeClass('expand');
      },
      {
        metadata: false,
        clean,
      },
    );
  }
};

$(() => {
  Object.assign(dotclear, dotclear.getData('dm_pending'));
  $.expandContent({
    lines: $('#pending-posts li.line'),
    callback: dotclear.dmPendingPostsView,
  });
  $('#pending-posts ul').addClass('expandable');
  if (dotclear.dmPendingPosts_Counter) {
    let icon = $('#dashboard-main #icons p a[href="posts.php"]');
    if (!icon.length) {
      icon = $('#dashboard-main #icons p #icon-process-posts-fav');
    }
    if (icon.length) {
      // Icon exists on dashboard
      // First pass
      dotclear.dmPendingPostsCount(icon);
      // Then fired every 60 seconds
      dotclear.dbPendingPostsCount_Timer = setInterval(dotclear.dmPendingPostsCount, 60 * 1000, icon);
    }
  }
  $.expandContent({
    lines: $('#pending-comments li.line'),
    callback: dotclear.dmPendingCommentsView,
  });
  $('#pending-comments ul').addClass('expandable');
  if (!dotclear.dmPendingComments_Counter) {
    return;
  }
  let icon = $('#dashboard-main #icons p a[href="comments.php"]');
  if (!icon.length) {
    icon = $('#dashboard-main #icons p #icon-process-comments-fav');
  }
  if (icon.length) {
    // Icon exists on dashboard
    // First pass
    dotclear.dmPendingCommentsCount(icon);
    // Then fired every 60 seconds
    dotclear.dbPendingCommentsCount_Timer = setInterval(dotclear.dmPendingCommentsCount, 60 * 1000, icon);
  }
});
