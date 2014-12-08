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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class FTCEMGDespesaPrev extends Persistente
{
    public function FTCEMGDespesaPrev()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_despesa_prev');

        $this->AddCampo('exercicio'    , 'varchar' , false , '' , false , false );
        $this->AddCampo('cod_entidade' , 'varchar' , false , '' , false , false );
        $this->AddCampo('dt_inicial'   , 'integer' , false , '' , false , false );
        $this->AddCampo('dt_final'     , 'integer' , false , '' , false , false );
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT despPrevSocInatPens
                 , despReservaContingencia
                 , despOutrasReservas
                 , codTipo
                 , despesasPrevIntra
                 , despCorrentes
                 , despCapital
                 , outrosBeneficios
                 , contPrevidenciaria
                 , outrasDespesas
              FROM ".$this->getTabela()."('".$this->getDado('exercicio')."','".$this->getDado('cod_entidade')."','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."') as
           retorno ( despPrevSocInatPens     numeric(14,2)
                   , despReservaContingencia numeric(14,2)
                   , despOutrasReservas      numeric(14,2)
                   , codTipo                 integer
                   , despesasPrevIntra       numeric(14,2)
                   , despCorrentes           numeric(14,2)
                   , despCapital             numeric(14,2)
                   , outrosBeneficios        numeric(14,2)
                   , contPrevidenciaria      numeric(14,2)
                   , outrasDespesas          numeric(14,2) );
        ";

        return $stSql;
    }
}
?>
