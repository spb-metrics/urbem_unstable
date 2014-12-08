<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página de
  * Data de criação : 18/11/2008

  * @copyright CCA Consultoria de Gestão Pública S/S Ltda.
  * @link http://www.ccanet.com.br CCA Consultoria de Gestão Pública S/S Ltda.

  * @author Analista: Gelson
  * @author Programador: Vitor Hugo

  $Id: TPatrimonioReavaliacao.class.php 42696 2009-10-16 19:38:32Z diogo.zarpelon $

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioReavaliacao extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */
    public function TPatrimonioReavaliacao()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.reavaliacao');
        $this->setCampoCod('cod_reavaliacao');
        $this->setComplementoChave('cod_bem');
        $this->AddCampo('cod_reavaliacao','integer',true,'',true,false);
        $this->AddCampo('cod_bem','integer',true,'',true,false);
        $this->AddCampo('dt_reavaliacao','date',false,'',false,false);
        $this->AddCampo('vida_util','integer',false,'',false,false);
        $this->AddCampo('vl_reavaliacao','numeric',true,'14.2',false,false);
        $this->AddCampo('motivo','varchar',true,'100',false,false);
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT
                   reavaliacao.cod_reavaliacao
                 , reavaliacao.cod_bem
                 , TO_CHAR(reavaliacao.dt_reavaliacao,'dd/mm/yyyy') AS dt_reavaliacao
                 , reavaliacao.vida_util
                 , reavaliacao.vl_reavaliacao
                 , reavaliacao.motivo
              FROM patrimonio.bem
              JOIN patrimonio.reavaliacao
                ON patrimonio.reavaliacao.cod_bem = patrimonio.bem.cod_bem
             WHERE ";
        $stOrder = " ORDER BY reavaliacao.dt_reavaliacao ";
        if ($this->getDado('cod_bem')) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."   AND ";
        }

        return substr($stSql,0,-6).$stOrder;
    }

    public function recuperaUltimaReavaliacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaUltimaReavaliacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaUltimaReavaliacao()
    {
        $stSql = "
          SELECT
                 reavaliacao.cod_reavaliacao
               , reavaliacao.cod_bem
               , TO_CHAR(reavaliacao.dt_reavaliacao,'dd/mm/yyyy') AS dt_reavaliacao
               , reavaliacao.vida_util
               , reavaliacao.vl_reavaliacao
               , reavaliacao.motivo

            FROM patrimonio.reavaliacao

      INNER JOIN patrimonio.bem
              ON bem.cod_bem = reavaliacao.cod_bem

           WHERE 1=1 ";

        if ($this->getDado('cod_bem')) {
            $stSql .= " AND bem.cod_bem = ".$this->getDado('cod_bem');
        }

        $stSql .= " ORDER BY dt_reavaliacao DESC LIMIT 1";

        return $stSql;
    }

}

?>
