//
//  ViewController.swift
//  Doação NFP
//
//  Created by Joao Pedro on 5/9/16.
//  Copyright © 2016 Joao Pedro. All rights reserved.
//

import UIKit

class HistoryViewController: UITableViewController{
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return DoacaoSingleton.sharedInstance.historico.count
    }
    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("historyCell", forIndexPath: indexPath)
        cell.textLabel?.text = DoacaoSingleton.sharedInstance.historico[indexPath.row].ong!.Nome
        
        
        var dateFormatter = NSDateFormatter()
        dateFormatter.dateFormat = "dd-MM-yyyy hh:mm"
        var DateInFormat = dateFormatter.stringFromDate(DoacaoSingleton.sharedInstance.historico[indexPath.row].data)
        
        
        cell.detailTextLabel?.text = DateInFormat
        return cell
    }
    
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        tableView.deselectRowAtIndexPath(indexPath, animated: false)
    }
    
    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        return 1
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
    }
    
    override func viewDidAppear(animated: Bool) {
        super.viewDidAppear(animated)
        self.tableView.reloadData()
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
}
