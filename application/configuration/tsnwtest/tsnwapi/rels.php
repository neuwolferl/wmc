<?php
$rels = array(
	'NAMES' => array(
		'modcomments' => 'tools.comments.modcomments',
		'users' => 'tools.users.vtusers',
		'accounts' => 'crm.cd.accounts',
		'contacts' => 'crm.cd.contacts',
		'events' => 'crm.rm.events',
		'calendar' => 'crm.rm.calendars',
		'invoice' => 'bpm.eco.invoices',
		'collections' => 'bpm.eco.collections',
		'banks' => 'bpm.eco.banks',
		'pjtcollections' => 'bpm.eco.project_collections',
		'pjtinvoices' => 'bpm.eco.project_invoices',
		'potentials' => 'bpm.com.potentials',
		'quotes' => 'bpm.com.quotes',
		'salesorder' => 'bpm.com.salesorders',
		'services' => 'bpm.com.services',
		'project' => 'bpm.pjm.projects',
		'projectmilestone' => 'bpm.pjm.project_milestones',
		'tmpmilestones' => 'bpm.pjm.temporary_milestones',
		'projecttask' => 'bpm.pjm.project_tasks'
		),
	'PREFIXES' => array(
		'tools.comments.modcomments' => '28',
		'tools.users.vtusers' => '19',
		'crm.cd.accounts' => '11',
		'crm.cd.contacts' => '12',
		'crm.rm.events' => '18',
		'crm.rm.calendars' => '9',
		'bpm.eco.invoices' => '7',
		'bpm.eco.collections' => '',
		'bpm.eco.banks' => '',
		'bpm.eco.project_collections' => '',
		'bpm.eco.project_invoices' => '',
		'bpm.com.potentials' => '13',
		'bpm.com.quotes' => '4',
		'bpm.com.salesorders' => '6',
		'bpm.com.services' => '26',
		'bpm.pjm.projects' => '31',
		'bpm.pjm.project_milestones' => '29',
		'bpm.pjm.temporary_milestones' => '',
		'bpm.pjm.project_tasks' => '30'
		),
	'tools.comments.modcomments' => array(
		'direct' => array(
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'related_to'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'related_to'
				),
			array(
				'refs' => 'bpm.com.potentials',
				'via' => 'related_to'
				),
			array(
				'refs' => 'bpm.pjm.project_tasks',
				'via' => 'related_to'
				),
			array(
				'refs' => 'bpm.pjm.projects',
				'via' => 'related_to'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'creator'
				),
			array(
				'refs' => 'tools.modcomments.comments',
				'via' => 'parent_comments'
				)
			),
		'inverse' => array(
			)
		),
	'tools.users.vtusers' => array(
		'direct' => array(
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'reports_to_id'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'creator'
				),
			array(
				'refed' => 'tools.users.vtusers',
				'by' => 'reports_to_id'
				),
			array(
				'refed' => 'crm.cd.accounts',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'crm.cd.accounts',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'crm.cd.contacts',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'crm.cd.contacts',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'crm.rm.events',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'crm.rm.events',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.eco.invoices',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.eco.invoices',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.com.potentials',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.com.potentials',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.com.quotes',
				'by' => 'assigned_user_id1'
				),
			array(
				'refed' => 'bpm.com.quotes',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.com.quotes',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.com.salesorders',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.com.salesorders',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.com.services',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.com.services',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.pjm.projects',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.pjm.projects',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.pjm.project_milestones',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.pjm.project_milestones',
				'by' => 'modifiedby'
				),
			array(
				'refed' => 'bpm.pjm.project_tasks',
				'by' => 'assigned_user_id'
				),
			array(
				'refed' => 'bpm.pjm.project_tasks',
				'by' => 'modifiedby'
				)
			)
		),
	'crm.cd.accounts' => array(
		'direct' => array(
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'account_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'related_to'
				),
			array(
				'refed' => 'crm.cd.accounts',
				'by' => 'account_id'
				),
			array(
				'refed' => 'crm.cd.contacts',
				'by' => 'account_id'
				),
			array(
				'refed' => 'crm.rm.events',
				'by' => 'parent_id'
				),
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'parent_id'
				),
			array(
				'refed' => 'bpm.eco.invoices',
				'by' => 'account_id'
				),
			array(
				'refed' => 'bpm.eco.collections',
				'by' => 'accountid'
				),
			array(
				'refed' => 'bpm.com.potentials',
				'by' => 'related_to'
				),
			array(
				'refed' => 'bpm.com.quotes',
				'by' => 'account_id'
				),
			array(
				'refed' => 'bpm.com.salesorders',
				'by' => 'account_id'
				),
			array(
				'refed' => 'bpm.pjm.projects',
				'by' => 'linktoaccountscontacts'
				)
			)
		),
	'crm.cd.contacts' => array(
		'direct' => array(
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'account_id'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'contact_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'related_to'
				),
			array(
				'refed' => 'crm.cd.contacts',
				'by' => 'contact_id'
				),
			array(
				'refed' => 'crm.rm.events',
				'by' => 'contact_id'
				),
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'contact_id'
				),
			array(
				'refed' => 'bpm.eco.invoices',
				'by' => 'contact_id'
				),
			array(
				'refed' => 'bpm.com.potentials',
				'by' => 'related_to'
				),
			array(
				'refed' => 'bpm.com.quotes',
				'by' => 'contact_id'
				),
			array(
				'refed' => 'bpm.com.salesorders',
				'by' => 'contact_id'
				),
			array(
				'refed' => 'bpm.pjm.projects',
				'by' => 'linktoaccountscontacts'
				)
			)
		),
	'bpm.com.potentials' => array(
		'direct' => array(
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'related_to'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'related_to'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'related_to'
				),
			array(
				'refed' => 'crm.rm.events',
				'by' => 'parent_id'
				),
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'parent_id'
				),
			array(
				'refed' => 'bpm.com.quotes',
				'by' => 'potential_id'
				),
			array(
				'refed' => 'bpm.com.salesorders',
				'by' => 'potential_id'
				)
			)
		),
	'bpm.pjm.project_tasks' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.pjm.projects',
				'via' => 'projectid'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'related_to'
				)
			)
		),
	'bpm.pjm.projects' => array(
		'direct' => array(
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'linktoaccountscontacts'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'linktoaccountscontacts'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				),
			array(
				'refs' => 'bpm.com.salesorders',
				'via' => 'cf_691'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'related_to'
				),
			array(
				'refed' => 'bpm.eco.project_collections',
				'by' => 'projectid'
				),
			array(
				'refed' => 'bpm.eco.project_invoices',
				'by' => 'projectid'
				),
			array(
				'refed' => 'bpm.pjm.project_milestones',
				'by' => 'projectid'
				),
			array(
				'refed' => 'bpm.pjm.temporary_milestones',
				'by' => 'projectid'
				),
			array(
				'refed' => 'bpm.pjm.project_tasks',
				'by' => 'projectid'
				)
			)
		),
	'tools.modcomments.comments' => array(
		'direct' => array(
			),
		'inverse' => array(
			array(
				'refed' => 'tools.comments.modcomments',
				'by' => 'parent_comments'
				)
			)
		),
	'crm.rm.events' => array(
		'direct' => array(
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'bpm.com.potentials',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'contact_id'
				)
			),
		'inverse' => array(
			)
		),
	'crm.rm.calendars' => array(
		'direct' => array(
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'bpm.com.quotes',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'bpm.com.salesorders',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'bpm.eco.invoices',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'bpm.com.potentials',
				'via' => 'parent_id'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'contact_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			)
		),
	'bpm.com.quotes' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.com.potentials',
				'via' => 'potential_id'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'contact_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id1'
				),
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'account_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'parent_id'
				),
			array(
				'refed' => 'bpm.com.salesorders',
				'by' => 'quote_id'
				)
			)
		),
	'bpm.com.salesorders' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.com.potentials',
				'via' => 'potential_id'
				),
			array(
				'refs' => 'bpm.com.quotes',
				'via' => 'quote_id'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'contact_id'
				),
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'account_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'parent_id'
				),
			array(
				'refed' => 'bpm.eco.invoices',
				'by' => 'salesorder_id'
				),
			array(
				'refed' => 'bpm.pjm.projects',
				'by' => 'cf_691'
				)
			)
		),
	'bpm.eco.invoices' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.com.salesorders',
				'via' => 'salesorder_id'
				),
			array(
				'refs' => 'crm.cd.contacts',
				'via' => 'contact_id'
				),
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'account_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			array(
				'refed' => 'crm.rm.calendars',
				'by' => 'parent_id'
				)
			)
		),
	'bpm.eco.collections' => array(
		'direct' => array(
			array(
				'refs' => 'crm.cd.accounts',
				'via' => 'accountid'
				)
			),
		'inverse' => array(
			)
		),
	'bpm.eco.banks' => array(
		'direct' => array(
			),
		'inverse' => array(
			)
		),
	'bpm.eco.project_collections' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.com.salesorder',
				'via' => 'salesorderid'
				),
			array(
				'refs' => 'bpm.pjm.projects',
				'via' => 'projectid'
				)
			),
		'inverse' => array(
			)
		),
	'bpm.com.salesorder' => array(
		'direct' => array(
			),
		'inverse' => array(
			array(
				'refed' => 'bpm.eco.project_collections',
				'by' => 'salesorderid'
				),
			array(
				'refed' => 'bpm.eco.project_invoices',
				'by' => 'salesorderid'
				)
			)
		),
	'bpm.eco.project_invoices' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.com.salesorder',
				'via' => 'salesorderid'
				),
			array(
				'refs' => 'bpm.pjm.projects',
				'via' => 'projectid'
				)
			),
		'inverse' => array(
			)
		),
	'bpm.com.services' => array(
		'direct' => array(
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				)
			),
		'inverse' => array(
			)
		),
	'bpm.pjm.project_milestones' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.pjm.projects',
				'via' => 'projectid'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'assigned_user_id'
				),
			array(
				'refs' => 'tools.users.vtusers',
				'via' => 'modifiedby'
				)
			),
		'inverse' => array(
			)
		),
	'bpm.pjm.temporary_milestones' => array(
		'direct' => array(
			array(
				'refs' => 'bpm.pjm.projects',
				'via' => 'projectid'
				)
			),
		'inverse' => array(
			)
		)
	);
?>