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
      var ret = $('rsp>count', data).attr('ret');
      if (ret != dotclear.dbPendingPostsCount_Counter) {
        // First pass or counter changed
        var icon = $('#dashboard-main #icons p a[href="posts.php?status=-2"]');
        if (icon.length) {
          // Update count if exists
          var nb_label = icon.children('span.db-icon-title-dm-pending');
          if (nb_label.length) {
            nb_label.text(ret);
          }
        } else {
          if (ret != '') {
            // Add full element (link + counter)
            icon = $('#dashboard-main #icons p a[href="posts.php"]');
            if (icon.length) {
              var xml = ' <a href="posts.php?status=-2"><span class="db-icon-title-dm-pending">' + ret + '</span></a>';
              icon.after(xml);
            }
          }
        }
        var nb = $('rsp>count', data).attr('nb');
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
  });
};
dotclear.dmPendingPostsView = function(line, action) {
  action = action || 'toggle';
  var id = $(line).attr('id').substr(4);
  var li = document.getElementById('dmppe' + id);
  if (!li && (action == 'toggle' || action == 'open')) {
    li = document.createElement('li');
    li.id = 'dmppe' + id;
    li.className = 'expand';
    // Get content
    $.get('services.php', {
      f: 'getPostById',
      id: id,
      post_type: ''
    }, function(data) {
      var rsp = $(data).children('rsp')[0];
      if (rsp.attributes[0].value == 'ok') {
        var content = $(rsp).find('post_display_excerpt').text() + ' ' + $(rsp).find('post_display_content').text();
        if (content) {
          $(li).append(content);
        }
      } else {
        window.alert($(rsp).find('message').text());
      }
    });
    $(line).toggleClass('expand');
    line.parentNode.insertBefore(li, line.nextSibling);
  } else if (li && li.style.display == 'none' && (action == 'toggle' || action == 'open')) {
    $(li).css('display', 'block');
    $(line).addClass('expand');
  } else if (li && li.style.display != 'none' && (action == 'toggle' || action == 'close')) {
    $(li).css('display', 'none');
    $(line).removeClass('expand');
  }
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
      var ret = $('rsp>count', data).attr('ret');
      if (ret != dotclear.dbPendingCommentsCount_Counter) {
        // First pass or counter changed
        var icon = $('#dashboard-main #icons p a[href="comments.php?status=-1"]');
        if (icon.length) {
          // Update count if exists
          var nb_label = icon.children('span.db-icon-title-dm-pending');
          if (nb_label.length) {
            nb_label.text(ret);
          }
        } else {
          if (ret != '') {
            // Add full element (link + counter)
            icon = $('#dashboard-main #icons p a[href="comments.php"]');
            if (icon.length) {
              var xml = ' <a href="comments.php?status=-1"><span class="db-icon-title-dm-pending">' + ret + '</span></a>';
              icon.after(xml);
            }
          }
        }
        var nb = $('rsp>count', data).attr('nb');
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
  });
};
dotclear.dmPendingCommentsView = function(line, action) {
  action = action || 'toggle';
  var id = $(line).attr('id').substr(4);
  var li = document.getElementById('dmpce' + id);
  if (!li && (action == 'toggle' || action == 'open')) {
    li = document.createElement('li');
    li.id = 'dmpce' + id;
    li.className = 'expand';
    // Get content
    $.get('services.php', {
      f: 'getCommentById',
      id: id
    }, function(data) {
      var rsp = $(data).children('rsp')[0];
      if (rsp.attributes[0].value == 'ok') {
        var content = $(rsp).find('comment_display_content').text();
        if (content) {
          $(li).append(content);
        }
      } else {
        window.alert($(rsp).find('message').text());
      }
    });
    $(line).toggleClass('expand');
    line.parentNode.insertBefore(li, line.nextSibling);
  } else if (li && li.style.display == 'none' && (action == 'toggle' || action == 'open')) {
    $(li).css('display', 'block');
    $(line).addClass('expand');
  } else if (li && li.style.display != 'none' && (action == 'toggle' || action == 'close')) {
    $(li).css('display', 'none');
    $(line).removeClass('expand');
  }
};

$(function() {
  $.expandContent({
    lines: $('#pending-posts li.line'),
    callback: dotclear.dmPendingPostsView
  });
  $('#pending-posts ul').addClass('expandable');
  var icon;
  if (dotclear.dmPendingPosts_Counter) {
    icon = $('#dashboard-main #icons p a[href="posts.php"]');
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
    icon = $('#dashboard-main #icons p a[href="comments.php"]');
    if (icon.length) {
      // Icon exists on dashboard
      // First pass
      dotclear.dmPendingCommentsCount();
      // Then fired every 60 seconds
      dotclear.dbPendingCommentsCount_Timer = setInterval(dotclear.dmPendingCommentsCount, 60 * 1000);
    }
  }
});
