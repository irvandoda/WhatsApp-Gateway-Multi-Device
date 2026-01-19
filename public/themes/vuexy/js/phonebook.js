// on ready
let activePhoneBook = undefined;
function processLoadMore() {
  $(".load-more").html(
    '<i class="ti tabler-loader icon-spin me-2 text-primary"></i> Loading...'
  ).attr("disabled", true);
}
function processLoadMoreDone() {
  $(".load-more").html('<i class="ti tabler-refresh me-1"></i> Load More').attr("disabled", false);
  $(".phone-book-list").animate({ scrollTop: $(".phone-book-list").prop("scrollHeight") }, 800);
}
function getPhoneBook(page = 1, search = "") {
  if (page > 1) {
    processLoadMore();
  }
  $.ajax({
    url: "/get-phonebook?page=" + page + "&search=" + search,
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (page == 1) {
        $(".phone-book-list").empty();
      }
      $(".phone-book-list").append(data.html);
      $(".load-phonebook").empty();
      if (data.current_page == data.last_page) {
        $(".load-more").hide();
      } else {
        if (page > 1) {
          processLoadMoreDone();
        }
        $(".load-more").attr("data-page", data.current_page + 1);
        $(".load-more").show();
      }
    },
  });
}

function clearPhonebook() {
  //confirm using default confirm
  if (confirm("Are you sure?")) {
    $.ajax({
      url: "/clear-phonebook",
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      method: "DELETE",
      dataType: "json",
      success: function (data) {
        if (data.error) {
          notyf.error(data.msg);
        } else {
          notyf.success(data.msg);
          getPhoneBook();
        }
      },
    });
  } else {
    notyf.error("Canceled");
  }
}

function getContact(id, page = 1, search = "") {
  $.ajax({
    url: `/get-contact/${id}?page=${page}&search=${search}`,
    dataType: "json",
    method: "GET",
    success: function (data) {
      if (page == 1) {
        $(".contacts-list").html(data.html);
      } else {
        $(".contacts-list").append(data.html);
        // remove load more button
        $(".contacts-list .load-more-contact").remove();
      }
	  $(".process-get-contact").remove();

      activePhoneBook = id;

      if (data.start_page < data.last_page) {
		  $(".contacts-list").append(
			`<div class="text-center mt-3">
			   <button class="btn btn-outline-secondary btn-sm load-more-contact"
					   data-page="${data.start_page + 1}"
					   data-phonebook-id="${id}">
				 <i class="ti tabler-refresh me-1"></i> Load More
			   </button>
			 </div>`
		  );
		}
    },
  });
}

function clickPhoneBook(id, element) {
  $(".contacts-list").html(
    '<div class="d-flex justify-content-center align-items-center p-5 w-100">' +
      '<i class="ti tabler-loader icon-spin fs-3 text-primary"></i>' +
    '</div>'
  );
  $(".single-phonebook").removeClass("active");
  $(element).addClass("active");
  getContact(id);
}


function addContact() {
  if (activePhoneBook == undefined) {
    notyf.error("Please select phonebook");
    return;
  }

  $("#addContact").modal("show");
}

function deleteContact(id) {
  $.ajax({
    url: `/contact/delete/${id}`,
    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    method: "DELETE",
    dataType: "json",
    success: function (data) {
      if (data.error) {
        notyf.error(data.msg);
      } else {
        notyf.success(data.msg);
        $(`#contact-${id}`).remove();
      }
    },
  });
}

function deleteAllContact() {
  if (activePhoneBook === undefined) {
    notyf.error("Please select phonebook");
    return;
  }
  // confirm delete using alert
  if (confirm("Are you sure you want to delete all contacts?")) {
    $.ajax({
      url: `/contact/delete-all/${activePhoneBook}`,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      method: "DELETE",
      dataType: "json",
      success: function (data) {
        if (data.error) {
          notyf.error(data.msg);
          return;
        } else {
          notyf.success(data.msg);
          $(".contacts-list").html(
			  '<div class="d-flex justify-content-center align-items-center p-5 w-100">' +
				'<i class="ti tabler-loader icon-spin fs-3 text-primary"></i>' +
			  '</div>'
			);
          getContact(activePhoneBook);
        }
      },
    });
  } else {
    notyf.error("Delete all contacts canceled");
    return;
  }
}


// mport contact
function importContact() {
  if (activePhoneBook == undefined) {
    notyf.error("Please select phonebook");
    return;
  }
  $("#importContacts").modal("show");
}

function exportContact() {
  if (activePhoneBook == undefined) {
    notyf.error("Please select phonebook");
    return;
  }
  window.location.href = `/contact/export/${activePhoneBook}`;
}

window.onload = function() {
	$(document).ready(function() {
		
  $(".chat-toggle-btn").on("click", function() {
		$(".chat-wrapper").toggleClass("chat-toggled")
	}), $(".chat-toggle-btn-mobile").on("click", function() {
		$(".chat-wrapper").removeClass("chat-toggled")
	}), $(".email-toggle-btn").on("click", function() {
		$(".email-wrapper").toggleClass("email-toggled")
	}), $(".email-toggle-btn-mobile").on("click", function() {
		$(".email-wrapper").removeClass("email-toggled")
	}), $(".compose-mail-btn").on("click", function() {
		$(".compose-mail-popup").show()
	}), $(".compose-mail-close").on("click", function() {
		$(".compose-mail-popup").hide()
	})

  $(".load-phonebook").append(
    ' <i class="bx bx-loader bx-spin font-size-18 text-primary me-2"></i>'
  );

  getPhoneBook();
  // load more
  $(".load-more").click(function () {
  const page = $(this).data("page");
  processLoadMore();
  getPhoneBook(page);
});
  
$(document).on("click", ".load-more-contact", function () {
  const page = $(this).data("page");
  const phonebookId = $(this).data("phonebook-id");
  $(this).html(
    '<i class="ti tabler-loader icon-spin me-2 text-primary"></i> Loading...'
  );
  getContact(phonebookId, page);
});

$(".search-contact").on("keyup", function () {
  // debounce
  if (activePhoneBook == undefined) return;
  var search = $(this).val();
  clearTimeout($.data(this, "timer"));
  var wait = setTimeout(function () {
    $(".contacts-list").html(
	  '<div class="d-flex justify-content-center align-items-center p-5 w-100">' +
		'<i class="ti tabler-loader icon-spin fs-3 text-primary"></i>' +
	  '</div>'
	);
    getContact(activePhoneBook, 1, search);
  }, 500);

  $(this).data("timer", wait);
});

$(".search-phonebook").on("keyup", function () {
  // debounce
  var search = $(this).val();
  clearTimeout($.data(this, "timer"));
  var wait = setTimeout(function () {
    $(".load-phonebook").html(
      '<div class="d-flex justify-content-center align-items-center" style="height: 100%;"><i class="bx bx-loader bx-spin font-size-18 text-primary me-2"></i></div>'
    );
    getPhoneBook(1, search);
  }, 500);
});
	
$(".add-contact-form").submit(function (e) {
  e.preventDefault();

  const phonebook_id = activePhoneBook;
  $(".input_phonebookid").val(phonebook_id);
  const data = $(this).serialize();
  $.ajax({
    url: "/contact/store",
    method: "POST",
    data: data,
    dataType: "json",
    success: function (data) {
      if (data.error) {
        notyf.error(data.msg);
        return;
      } else {
        notyf.success(data.msg);
        $("#addContact").modal("hide");
        $(".contacts-list").html(
		  '<div class="d-flex justify-content-center align-items-center p-5 w-100">' +
			'<i class="ti tabler-loader icon-spin fs-3 text-primary"></i>' +
		  '</div>'
		);
        // reset form
        $(".add-contact-form")[0].reset();
        getContact(activePhoneBook);
        return;
      }
    },
  });
});

$("#import-contact-form").submit(function (e) {
  e.preventDefault();
  const data = new FormData(this);
  data.append("phonebook_id", activePhoneBook);
  data.append("_token", $('meta[name="csrf-token"]').attr("content"));
  data.append("file", $("#fileContacts")[0].files[0]);

  $.ajax({
    url: "/contact/import",
    method: "POST",
    data: data,
    dataType: "json",
    contentType: false,
    processData: false,

    success: function (data) {
      console.log(data);
      if (data.error) {
        notyf.error(data.msg);
        return;
      } else {
        notyf.success(data.msg);
        $("#importContacts").modal("hide");
        $(".contacts-list").html(
		  '<div class="d-flex justify-content-center align-items-center p-5 w-100">' +
			'<i class="ti tabler-loader icon-spin fs-3 text-primary"></i>' +
		  '</div>'
		);
        // reset form
        $("#import-contact-form")[0].reset();
        getContact(activePhoneBook);
        return;
      }
    },
  });
});
	});
};