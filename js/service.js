/*global $, dotclear */
'use strict';

dotclear.dmPendingPostsCount = function() {
  var params = {
    f: 'dmPendingPostsCount',
    xd_check: dotclear.nonce,
  };
  $.get('services.php', params, function(data) {
    if ($('rsp[status=failed]', data).length > 0) {
      // For debugging purpose only:
      // console.log($('rsp',data).attr('message'));
      window.console.log('Dotclear REST server error');
    } else {
      var nb = $('rsp>count', data).attr('ret');
      if (nb != dotclear.dbPendingPostsCount_Counter) {
        // First pass or counter changed
        var icon = $('#dashboard-main #icons p a[href="posts.php?status=-2"]');
        if (icon.length) {
          // Update count if exists
          var nb_label = icon.children('span.db-icon-title-dm-pending');
          if (nb_label.length) {
            nb_label.text(nb);
          }
        } else {
          if (nb != '') {
            // Add full element (link + counter)
            var icon = $('#dashboard-main #icons p a[href="posts.php"]');
            if (icon.length) {
              var xml = ' <a href="posts.php?status=-2"><span class="db-icon-title-dm-pending">' + nb + '</span></a>';
              icon.after(xml);
            }
          }
        }
        // Store current counter
        dotclear.dbPendingPostsCount_Counter = nb;
      }
    }
  });
};

dotclear.dmPendingCommentsCount = function() {
  var params = {
    f: 'dmPendingCommentsCount',
    xd_check: dotclear.nonce,
  };
  $.get('services.php', params, function(data) {
    if ($('rsp[status=failed]', data).length > 0) {
      // For debugging purpose only:
      // console.log($('rsp',data).attr('message'));
      window.console.log('Dotclear REST server error');
    } else {
      var nb = $('rsp>count', data).attr('ret');
      if (nb != dotclear.dbPendingCommentsCount_Counter) {
        // First pass or counter changed
        var icon = $('#dashboard-main #icons p a[href="comments.php?status=-1"]');
        if (icon.length) {
          // Update count if exists
          var nb_label = icon.children('span.db-icon-title-dm-pending');
          if (nb_label.length) {
            nb_label.text(nb);
          }
        } else {
          if (nb != '') {
            // Add full element (link + counter)
            var icon = $('#dashboard-main #icons p a[href="comments.php"]');
            if (icon.length) {
              var xml = ' <a href="comments.php?status=-1"><span class="db-icon-title-dm-pending">' + nb + '</span></a>';
              icon.after(xml);
            }
          }
        }
        // Store current counter
        dotclear.dbPendingCommentsCount_Counter = nb;
      }
    }
  });
};

$(function() {
  if (dotclear.dmPendingPosts_Counter) {
    var icon = $('#dashboard-main #icons p a[href="posts.php"]');
    if (icon.length) {
      // Icon exists on dashboard
      // First pass
      dotclear.dmPendingPostsCount();
      // Then fired every 60 seconds
      dotclear.dbPendingPostsCount_Timer = setInterval(dotclear.dmPendingPostsCount, 60 * 1000);
    }
  }
  if (dotclear.dmPendingComments_Counter) {
    var icon = $('#dashboard-main #icons p a[href="comments.php"]');
    if (icon.length) {
      // Icon exists on dashboard
      // First pass
      dotclear.dmPendingCommentsCount();
      // Then fired every 60 seconds
      dotclear.dbPendingCommentsCount_Timer = setInterval(dotclear.dmPendingCommentsCount, 60 * 1000);
    }
  }
});
