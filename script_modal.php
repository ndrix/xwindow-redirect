<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">New Snippet</h4>
    </div>
    <div class="modal-body">
      <div>
        <form class="form-horizontal">
          <div class="form-group">
            <label for="inputOrigUrl" class="col-sm-3 control-label">Original URL</label>
            <div class="col-sm-9">
              <input type="url" class="form-control exploit-input checkurl" id="inputOrigUrl" placeholder="Ex: https://www.bank.com" />
            </div>
          </div>
          <div class="form-group">
            <label for="inputNewUrl" class="col-sm-3 control-label">New URL</label>
            <div class="col-sm-9">
              <input type="url" class="form-control exploit-input checkurl" id="inputNewUrl" placeholder="Ex: http://www.fakebank.com/login.php" />
            </div>
          </div>
          <div class="form-group">
            <label for="inputType" class="col-sm-3 control-label">Script type</label>
            <div class="col-sm-9">
              <div class="radio">
                <label>
                  <input type="radio" name="inputType" id="inputType" value="interactive" checked="checked"/>
                  Interactive attack (using XHR)
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="inputType" id="inputType" value="timed" />
                  Timed attack (using setTimeout())
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="inputType" id="inputType" value="visibility" />
                  Visibility API attack ("visibilitychanged" event)
                </label>
              </div>
            </div>
          </div>
          <div class="form-group" id="showTimeout">
            <label for="inputTimeout" class="col-sm-3 control-label">Timeout</label>
            <div class="col-sm-3">
             <input type="number" min="1" max="1000" class="form-control exploit-input" id="inputTimeout" placeholder="in seconds, ex: 20" value="10"/>
            </div>
            <div class="col-sm-6">
              Seconds, Only for timeout
            </div>
          </div>
          <div class="form-group" id="showTimeout">
            <div class="col-sm-3"></div>
            <div class="col-sm-9">
              <button class="btn btn-default" id="createCode">Create code</button>
            </div>
          </div>

        </form>
      </div>
      <div id="exploitcode">
        <div id="warnings"></div>
        <code>
        </code>
      </div> <!-- exploitcode -->
    </div> <!-- modal body -->
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary" id="addAttack">Add Attack</button>
    </div>
  </div> <!-- content -->
</div> <!-- dialog -->

