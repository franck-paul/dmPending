/*global dotclear */
'use strict';

dotclear.ready(() => {
  dotclear.dmPending = dotclear.getData('dm_pending');

  const viewPost = (line, _action = 'toggle', event = null) => {
    dotclear.dmViewPost(line, 'dmppe', event.metaKey);
  };

  const getPostsCount = (icon) => {
    dotclear.services(
      'dmPendingPostsCount',
      (data) => {
        try {
          const response = JSON.parse(data);
          if (response?.success) {
            if (response?.payload.ret) {
              const { msg } = response.payload;
              if (msg !== undefined && msg !== dotclear.dmPending.postsCounter) {
                const href = icon.getAttribute('href');
                const param = `${href.includes('?') ? '&' : '?'}status=-2`;
                const url = `${href}${param}`;
                // First pass or counter changed
                const link = document.querySelector(`#dashboard-main #icons p a[href="${url}"]`);
                if (link) {
                  // Update count if exists
                  const nb_label = link.querySelector('span.db-icon-title-dm-pending');
                  if (nb_label) {
                    nb_label.textContent = msg;
                  }
                } else if (msg !== '' && icon.length) {
                  // Add full element (link + counter)
                  const xml = ` <a href="${url}"><span class="db-icon-title-dm-pending">${msg}</span></a>`;
                  icon.insertAdjacentHTML('afterEnd', xml);
                }
                const { nb } = response.payload;
                // Badge on module
                dotclear.badge(document.querySelector('#pending-posts'), {
                  id: 'dmpp',
                  value: nb,
                  remove: nb <= 0,
                  type: 'soft',
                });
                // Store current counter
                dotclear.dmPending.postsCounter = msg;
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

  const viewComment = (line, _action = 'toggle', event = null) => {
    dotclear.dmViewComment(line, 'dmpce', event.metaKey || line.classList.contains('sts-junk'));
  };

  const getCommentsCount = (icon) => {
    dotclear.services(
      'dmPendingCommentsCount',
      (data) => {
        try {
          const response = JSON.parse(data);
          if (response?.success) {
            if (response?.payload.ret) {
              const { msg } = response.payload;
              if (msg !== undefined && msg !== dotclear.dmPending.commentsCounter) {
                const href = icon.getAttribute('href');
                const param = `${href.includes('?') ? '&' : '?'}status=-1`;
                const url = `${href}${param}`;
                // First pass or counter changed
                const link = document.querySelector(`#dashboard-main #icons p a[href="${url}"]`);
                if (link) {
                  // Update count if exists
                  const nb_label = link.querySelector('span.db-icon-title-dm-pending');
                  if (nb_label) {
                    nb_label.textContent = msg;
                  }
                } else if (msg !== '') {
                  // Add full element (link + counter)
                  const xml = ` <a href="${url}"><span class="db-icon-title-dm-pending">${msg}</span></a>`;
                  icon.insertAdjacentHTML('afterEnd', xml);
                }
                const { nb } = response.payload;
                // Badge on module
                dotclear.badge(document.querySelector('#pending-comments'), {
                  id: 'dmpc',
                  value: nb,
                  remove: nb <= 0,
                  type: 'soft',
                });
                // Store current counter
                dotclear.dmPending.commentsCounter = msg;
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

  dotclear.expandContent({
    lines: document.querySelectorAll('#pending-posts li.line'),
    callback: viewPost,
  });
  for (const item of document.querySelectorAll('#pending-posts ul')) item.classList.add('expandable');

  dotclear.expandContent({
    lines: document.querySelectorAll('#pending-comments li.line'),
    callback: viewComment,
  });
  for (const item of document.querySelectorAll('#pending-comments ul')) item.classList.add('expandable');

  if (!dotclear.dmPending.autoRefresh) {
    return;
  }

  if (dotclear.dmPending.postsCounter) {
    let icon_posts = document.querySelector('#dashboard-main #icons p a[href="posts.php"]');
    if (!icon_posts) {
      icon_posts = document.querySelector('#dashboard-main #icons p #icon-process-posts-fav');
    }
    if (icon_posts) {
      // Icon exists on dashboard
      // First pass
      getPostsCount(icon_posts);
      // Then fired every 60 seconds
      dotclear.dmPending.postsTimer = setInterval(getPostsCount, (dotclear.dmPending.interval || 60) * 1000, icon_posts);
    }
  }
  if (dotclear.dmPending.commentsCounter) {
    let icon_comments = document.querySelector('#dashboard-main #icons p a[href="comments.php"]');
    if (!icon_comments) {
      icon_comments = document.querySelector('#dashboard-main #icons p #icon-process-comments-fav');
    }
    if (icon_comments) {
      // Icon exists on dashboard
      // First pass
      getCommentsCount(icon_comments);
      // Then fired every 60 seconds
      dotclear.dmPending.commentsTimer = setInterval(
        getCommentsCount,
        (dotclear.dmPending.interval || 60) * 1000,
        icon_comments,
      );
    }
  }
});
